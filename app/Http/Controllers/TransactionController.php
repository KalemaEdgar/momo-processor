<?php

namespace App\Http\Controllers;

use App\Models\Blacklist;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Whitelist;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    /**
     * @todo Have an API to return all transactions
     * @todo Have an API to return all transactions for a specific OVA
     * @todo Have an API to return all transactions for a specific phone number / MNO wallet
     */


    public function mtn(Request $request)
    {
        try {
            // Check client credentials -- done
            // Validate the parameters -- done
            // Post the transaction -- done
            // Respond back with the codes they suggested -- done
            // Those in whitelist -- SUCCESS (Reduce the balance on the dfcu account, log the transaction in the DB)
            // Those in blacklist -- FAIL (do nothing on balance, log the transaction in the DB)
            // Those in neither list -- PENDING for manual review (reduce the balance on the dfcu account, log transaction in the DB)

            info('Request:', [
                'MTN Transaction request for' => $request->creditAccount,
                'received_at' => now()->format('c')
            ]);

            // All encrypted values are encrypted using OpenSSL and the AES-256-CBC cipher.
            // Furthermore, all encrypted values are signed with a message authentication code (MAC).
            // The integrated message authentication code will prevent the decryption of any values that have been tampered with by malicious users
            $clientId = Crypt::decryptString($request->header('x-client-id'));
            $clientSecret = Crypt::decryptString($request->header('x-client-secret'));

            if (empty($clientId) || empty($clientSecret))
            {
                $response = [
                    'status' => 'fail',
                    'error' => 'Invalid client credentials',
                    'responded_at' => now()->format('c'),
                ];

                Log::error('Response:', $response);

                return response(collect($response), 400);
            }

            // Check if this client Id and client secret is matching from the DB
            if ( ! Auth::attempt(['client_id' => $clientId, 'password' => $clientSecret, 'blocked' => false]))
            {
                $response = [
                    'status' => 'fail',
                    'error' => 'Invalid client credentials',
                    'responded_at' => now()->format('c'),
                ];

                Log::error('Response:', $response);

                return response(collect($response), 400);
            }

            $validator = Validator::make($request->all(), [
                'reference' => 'bail|required|alpha_num|unique:transactions,reference',
                'creditAccount' => 'bail|required|regex:/^(07[0-9\s\-\+\(\)]*)$/|min:10|max:10',
                'amount' => 'required|numeric|between:5000,5000000',
                'transactionType' => 'required|string|max:20',
                'requestTime' => 'required|date_format:Y-m-d H:i:s',
            ]);

            if ($validator->fails()) {
                $response = [
                    'status' => 'fail',
                    'error' => $validator->errors()->first(),
                    'responded_at' => now()->format('c'),
                ];

                Log::error('Response:', $response);

                return response(collect($response), 400);
            }

            // Pick the OVA from the users table
            $ovas = User::where('client_id', $clientId)->firstOrFail('ova');

            $attributes = $validator->validated();

            // Check if the phone number is in a whitelist or blacklist
            if (Blacklist::where('phone', $attributes['creditAccount'])->exists())
            {
                Transaction::create([
                    'reference' => $attributes['reference'],
                    'client_id' => $clientId,
                    'debit_account' => $ovas['ova'],
                    'credit_account' => $attributes['creditAccount'],
                    'transaction_type' => $attributes['transactionType'],
                    'amount' => $attributes['amount'],
                    'created_by' => 'admin',
                    'client_ip' => $request->ip(),
                    'status' => 'Pending',
                    'reason' => 'Pending',
                ]);

                $response = [
                    'status' => 'Pending',
                    'message' => 'Pending. Please check status or contact customer care for support',
                    'responded_at' => now()->format('c'),
                ];

                Log::info('Response:', $response);

                return response($response, 202);
            }

            if (! Whitelist::where('phone', $attributes['creditAccount'])->exists())
            {
                Transaction::create([
                    'reference' => $attributes['reference'],
                    'client_id' => $clientId,
                    'debit_account' => $ovas['ova'],
                    'credit_account' => $attributes['creditAccount'],
                    'transaction_type' => $attributes['transactionType'],
                    'amount' => $attributes['amount'],
                    'created_by' => 'admin',
                    'client_ip' => $request->ip(),
                    'status' => 'Failed',
                    'reason' => 'Invalid wallet Id',
                ]);

                $response = [
                    'status' => 'Failed',
                    'message' => 'Invalid wallet Id. Please contact customer care for support',
                    'responded_at' => now()->format('c'),
                ];

                Log::info('Response:', $response);

                return response($response, 400);
            }

            // This means that the wallet Id is part of the whitelist
            Transaction::create([
                'reference' => $attributes['reference'],
                'client_id' => $clientId,
                'debit_account' => $ovas['ova'],
                'credit_account' => $attributes['creditAccount'],
                'transaction_type' => $attributes['transactionType'],
                'amount' => $attributes['amount'],
                'created_by' => 'admin',
                'client_ip' => $request->ip(),
                'status' => 'Successful',
                'reason' => 'Wallet credited successfully',

                // $table->string('status')->nullable();
                // $table->string('reason')->nullable();
                // $table->string('client_ip')->nullable();
                // $table->smallInteger('retries')->default(0);
                // // Reversal needed incase the transaction maxes out the retries and is still failing
                // $table->boolean('reversal_required')->default(false);
                // $table->boolean('reversed')->default('false');
                // $table->string('reversal_time')->nullable();
                // $table->string('reversal_status')->nullable();
                // $table->string('reversal_message')->nullable();
            ]);

            $response = [
                'status' => 'Successful',
                'message' => 'Transaction processed successfully',
                'responded_at' => now()->format('c'),
            ];

            Log::info('Response:', $response);

            return response($response, 200);

        } catch (Exception $ex) {
            $response['status'] = 'fail';
            $response['message'] = 'Request failed. Please try again in a minute or contact support';
            $response['responded_at'] = now()->format('c');

            Log::error('Exception:', ['code' => $ex->getCode(), 'message' => $ex->getMessage()]);
            Log::error('Response:', $response);

            return response(collect($response), 400);
        }
    }

    public function airtel(Request $request)
    {
        try {
            // Check client credentials -- done
            // Validate the parameters -- done
            // Post the transaction -- done
            // Respond back with the codes they suggested -- done
            // Those in whitelist -- SUCCESS (Reduce the balance on the dfcu account, log the transaction in the DB)
            // Those in blacklist -- FAIL (do nothing on balance, log the transaction in the DB)
            // Those in neither list -- PENDING for manual review (reduce the balance on the dfcu account, log transaction in the DB)

            info('Request:', [
                'Airtel Transaction request for' => $request->creditAccount,
                'received_at' => now()->format('c')
            ]);

            // All encrypted values are encrypted using OpenSSL and the AES-256-CBC cipher.
            // Furthermore, all encrypted values are signed with a message authentication code (MAC).
            // The integrated message authentication code will prevent the decryption of any values that have been tampered with by malicious users
            $clientId = Crypt::decryptString($request->header('x-client-id'));
            $clientSecret = Crypt::decryptString($request->header('x-client-secret'));

            if (empty($clientId) || empty($clientSecret))
            {
                $response = [
                    'status' => 'fail',
                    'error' => 'Invalid client credentials',
                    'responded_at' => now()->format('c'),
                ];

                Log::error('Response:', $response);

                return response(collect($response), 400);
            }

            // Check if this client Id and client secret is matching from the DB
            if ( ! Auth::attempt(['client_id' => $clientId, 'password' => $clientSecret, 'blocked' => false]))
            {
                $response = [
                    'status' => 'fail',
                    'error' => 'Invalid client credentials',
                    'responded_at' => now()->format('c'),
                ];

                Log::error('Response:', $response);

                return response(collect($response), 400);
            }

            $validator = Validator::make($request->all(), [
                'reference' => 'bail|required|alpha_num|unique:transactions,reference',
                'creditAccount' => 'bail|required|regex:/^(07[0-9\s\-\+\(\)]*)$/|min:10|max:10',
                'amount' => 'required|numeric|between:5000,5000000',
                'transactionType' => 'required|string|max:20',
                'requestTime' => 'required|date_format:Y-m-d H:i:s',
            ]);

            if ($validator->fails()) {
                $response = [
                    'status' => 'fail',
                    'error' => $validator->errors()->first(),
                    'responded_at' => now()->format('c'),
                ];

                Log::error('Response:', $response);

                return response(collect($response), 400);
            }

            // Pick the OVA from the users table
            $ovas = User::where('client_id', $clientId)->firstOrFail('ova');

            $attributes = $validator->validated();

            // Check if the phone number is in a whitelist or blacklist
            if (Blacklist::where('phone', $attributes['creditAccount'])->exists())
            {
                Transaction::create([
                    'reference' => $attributes['reference'],
                    'client_id' => $clientId,
                    'debit_account' => $ovas['ova'],
                    'credit_account' => $attributes['creditAccount'],
                    'transaction_type' => $attributes['transactionType'],
                    'amount' => $attributes['amount'],
                    'created_by' => 'admin',
                    'client_ip' => $request->ip(),
                    'status' => 'Pending',
                    'reason' => 'Pending',
                ]);

                $response = [
                    'status' => 'Failed',
                    'message' => 'Failed. Please try again or contact customer care for support',
                    'responded_at' => now()->format('c'),
                ];

                Log::info('Response:', $response);

                return response($response, 400);
            }

            if (! Whitelist::where('phone', $attributes['creditAccount'])->exists())
            {
                Transaction::create([
                    'reference' => $attributes['reference'],
                    'client_id' => $clientId,
                    'debit_account' => $ovas['ova'],
                    'credit_account' => $attributes['creditAccount'],
                    'transaction_type' => $attributes['transactionType'],
                    'amount' => $attributes['amount'],
                    'created_by' => 'admin',
                    'client_ip' => $request->ip(),
                    'status' => 'Failed',
                    'reason' => 'Invalid wallet Id',
                ]);

                $response = [
                    'status' => 'Failed',
                    'message' => 'Invalid wallet Id. Please contact customer care for support',
                    'responded_at' => now()->format('c'),
                ];

                Log::info('Response:', $response);

                return response($response, 400);
            }

            // This means that the wallet Id is part of the whitelist
            Transaction::create([
                'reference' => $attributes['reference'],
                'client_id' => $clientId,
                'debit_account' => $ovas['ova'],
                'credit_account' => $attributes['creditAccount'],
                'transaction_type' => $attributes['transactionType'],
                'amount' => $attributes['amount'],
                'created_by' => 'admin',
                'client_ip' => $request->ip(),
                'status' => 'Successful',
                'reason' => 'Wallet credited successfully',

                // $table->string('status')->nullable();
                // $table->string('reason')->nullable();
                // $table->string('client_ip')->nullable();
                // $table->smallInteger('retries')->default(0);
                // // Reversal needed incase the transaction maxes out the retries and is still failing
                // $table->boolean('reversal_required')->default(false);
                // $table->boolean('reversed')->default('false');
                // $table->string('reversal_time')->nullable();
                // $table->string('reversal_status')->nullable();
                // $table->string('reversal_message')->nullable();
            ]);

            $response = [
                'status' => 'Successful',
                'message' => 'Transaction processed successfully',
                'responded_at' => now()->format('c'),
            ];

            Log::info('Response:', $response);

            return response($response, 200);

        } catch (Exception $ex) {
            $response['status'] = 'fail';
            $response['message'] = 'Request failed. Please try again in a minute or contact support';
            $response['responded_at'] = now()->format('c');

            Log::error('Exception:', ['code' => $ex->getCode(), 'message' => $ex->getMessage()]);
            Log::error('Response:', $response);

            return response(collect($response), 400);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function show(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaction $transaction)
    {
        //
    }
}
