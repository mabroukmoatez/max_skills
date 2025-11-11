<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\User; 
use App\Models\PaymentAttempt; 
use App\Models\Payments; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserWelcomeMail;
use App\Models\UploadedFile;
use App\Models\Cour;

class PaymentController extends Controller
{
    // private $konnectBaseUrl = env('KONNECT_BASE_URL');
    // private $konnectApiKey = env('KONNECT_API_KEY');
    // private $konnectReceiverWalletId = env('KONNECT_RECEIVER_WALLET_ID');

    private $konnectBaseUrl = 'https://api.konnect.network/api/v2/'; // Sandbox Test Api
    private $konnectApiKey = '66cb84380e581535c3e2d8e2:aqCbkxv3GiU2W1iJRCk45OJhpS'; // Sandbox Test Api
    private $konnectReceiverWalletId = '66cb84390e581535c3e2d8ed';

    // List of random avatars
    public $randomAvatars = [
        '/storage/avatars/avatar-1.png',
        '/storage/avatars/avatar-2.png',
        '/storage/avatars/avatar-3.png',
        '/storage/avatars/avatar-4.png',
        '/storage/avatars/avatar-5.png',
        '/storage/avatars/avatar-6.png',
        '/storage/avatars/avatar-7.png',
        '/storage/avatars/avatar-8.png',
        '/storage/avatars/avatar-9.png',
        '/storage/avatars/avatar-10.png',
        '/storage/avatars/avatar-11.png',
        '/storage/avatars/avatar-12.png',
        '/storage/avatars/avatar-13.png',
        '/storage/avatars/avatar-14.png',
        '/storage/avatars/avatar-15.png',
        '/storage/avatars/avatar-16.png',
        '/storage/avatars/avatar-17.png',
        '/storage/avatars/avatar-18.png',
        '/storage/avatars/avatar-19.png',
        '/storage/avatars/avatar-20.png',
        '/storage/avatars/avatar-21.png',
        '/storage/avatars/avatar-22.png',
        '/storage/avatars/avatar-23.png',
        '/storage/avatars/avatar-24.png',
        '/storage/avatars/avatar-25.png',
        '/storage/avatars/avatar-26.png',
        '/storage/avatars/avatar-27.png',
        '/storage/avatars/avatar-28.png',
        '/storage/avatars/avatar-29.png',
        '/storage/avatars/avatar-30.png',
        '/storage/avatars/avatar-31.png',
        '/storage/avatars/avatar-32.png',
        '/storage/avatars/avatar-33.png',
        '/storage/avatars/avatar-34.png',
        '/storage/avatars/avatar-35.png',
        '/storage/avatars/avatar-36.png',
        '/storage/avatars/avatar-37.png',
        '/storage/avatars/avatar-38.png',
        '/storage/avatars/avatar-39.png',
        '/storage/avatars/avatar-40.png',
        '/storage/avatars/avatar-41.png',
        '/storage/avatars/avatar-42.png',
        '/storage/avatars/avatar-43.png',
        '/storage/avatars/avatar-44.png',
        '/storage/avatars/avatar-45.png',
        '/storage/avatars/avatar-46.png',
        '/storage/avatars/avatar-47.png',
        '/storage/avatars/avatar-48.png',
        '/storage/avatars/avatar-49.png',
        '/storage/avatars/avatar-50.png',
        '/storage/avatars/avatar-51.png',
        '/storage/avatars/avatar-52.png',
        '/storage/avatars/avatar-53.png',
        '/storage/avatars/avatar-54.png',
        '/storage/avatars/avatar-55.png',
        '/storage/avatars/avatar-56.png',
        '/storage/avatars/avatar-57.png',
        '/storage/avatars/avatar-58.png',
        '/storage/avatars/avatar-59.png',
        '/storage/avatars/avatar-60.png',
        '/storage/avatars/avatar-61.png',
        '/storage/avatars/avatar-62.png',
        '/storage/avatars/avatar-63.png',
        '/storage/avatars/avatar-64.png',
        '/storage/avatars/avatar-65.png',
        '/storage/avatars/avatar-66.png',
        '/storage/avatars/avatar-67.png',
        '/storage/avatars/avatar-68.png',
        '/storage/avatars/avatar-69.png',
        '/storage/avatars/avatar-70.png',
        '/storage/avatars/avatar-71.png',
        '/storage/avatars/avatar-72.png',
        '/storage/avatars/avatar-73.png',
        '/storage/avatars/avatar-74.png',
        '/storage/avatars/avatar-75.png',
        '/storage/avatars/avatar-76.png',
        '/storage/avatars/avatar-77.png',
        '/storage/avatars/avatar-78.png',
        '/storage/avatars/avatar-79.png',
        '/storage/avatars/avatar-80.png',
        '/storage/avatars/avatar-81.png',
        '/storage/avatars/avatar-82.png',
        '/storage/avatars/avatar-83.png',
        '/storage/avatars/avatar-84.png',
        '/storage/avatars/avatar-85.png',
        '/storage/avatars/avatar-86.png',
        '/storage/avatars/avatar-87.png',
        '/storage/avatars/avatar-88.png',
        '/storage/avatars/avatar-89.png',
        '/storage/avatars/avatar-90.png',
        '/storage/avatars/avatar-91.png',
        '/storage/avatars/avatar-92.png',
        '/storage/avatars/avatar-93.png',
        '/storage/avatars/avatar-94.png',
        '/storage/avatars/avatar-95.png',
        '/storage/avatars/avatar-96.png',
        '/storage/avatars/avatar-97.png',
        '/storage/avatars/avatar-98.png',
        '/storage/avatars/avatar-99.png',
        '/storage/avatars/avatar-100.png',
    ];

    public function initiatePayment(Request $request)
    {
        $amount = $request->input('amount');
        $courseId = $request->input('course_id');
        $userEmail = $request->input('email');
        $userFirstName = $request->input('first_name');
        $userLastName = $request->input('last_name');
        $userPhoneNumber = $request->input('phone_number');

        $amountInMills = (int) (str_replace('.', '', $amount));
        $generatedOrderId = 'COURSE_' . $courseId . '_' . uniqid();

        $client = new Client();

        $konnectBaseUrl = $this->konnectBaseUrl;
        $konnectApiKey = $this->konnectApiKey;
        $konnectReceiverWalletId = $this->konnectReceiverWalletId;

        try {
            $response = $client->post("{$konnectBaseUrl}payments/init-payment", [
                'headers' => [
                    'x-api-key' => $konnectApiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'receiverWalletId' => $konnectReceiverWalletId,
                    'token' => 'TND',
                    'amount' => $amountInMills,
                    'type' => 'immediate',
                    'description' => 'Payment for Course ID: ' . $courseId,
                    'acceptedPaymentMethods' => ['wallet', 'bank_card', 'e-DINAR'],
                    'lifespan' => 10,
                    'checkoutForm' => true,
                    'addPaymentFeesToAmount' => true,
                    'firstName' => $userFirstName ?? 'Customer',
                    'lastName' => $userLastName ?? 'Name',
                    'phoneNumber' => $userPhoneNumber ?? '00000000',
                    'email' => $userEmail ?? 'customer@example.com',
                    'orderId' => $generatedOrderId, // Your internal orderId
                    'theme' => 'dark',
                    'webhook' => null,
                    'silentWebhook' => false,
                    'successUrl' => route('payment.success', ['orderId' => $generatedOrderId]), // Pass your orderId
                    'failUrl' => route('payment.fail'),
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (isset($data['payUrl']) && isset($data['paymentRef'])) {
                // Store payment attempt details in the database
                PaymentAttempt::create([
                    'konnect_payment_ref' => $data['paymentRef'], // Konnect's unique payment ID
                    'order_id' => $generatedOrderId, // Your internal order ID
                    'amount' => $amount, // Store original amount
                    'course_id' => $courseId,
                    'email' => $userEmail,
                    'first_name' => $userFirstName,
                    'last_name' => $userLastName,
                    'phone_number' => $userPhoneNumber,
                    'status' => 'success', // Initial status
                    'konnect_pay_url' => $data['payUrl'],
                ]);

                Log::info('Initiated Konnect payment. Details stored in DB.', ['order_id' => $generatedOrderId, 'email' => $userEmail, 'konnect_payment_ref' => $data['paymentRef']]);

                return response()->json(['success' => true, 'redirect_url' => $data['payUrl']]);
            } else {
                Log::error('Konnect API: No payUrl or paymentRef returned', ['response' => $data]);
                return response()->json(['success' => false, 'message' => 'Failed to get payment URL from Konnect.']);
            }
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            $statusCode = $response->getStatusCode();
            $responseBody = $response->getBody()->getContents();
            Log::error("Konnect API Client Error ({$statusCode}): " . $responseBody, ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Konnect API client error: ' . $responseBody]);
        } catch (\Exception $e) {
            Log::error('Konnect Payment Initiation Error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['success' => false, 'message' => 'An error occurred during payment initiation.']);
        }
    }

    public function handleWebhook(Request $request)
    {
        $data = $request->all();
        Log::info('Konnect Webhook Received:', $data);

        if (isset($data['paymentRef']) && isset($data['status'])) {
            $paymentRef = $data['paymentRef'];
            $status = $data['status'];
            $orderId = $data['orderId'] ?? null;

            $paymentAttempt = PaymentAttempt::where('konnect_payment_ref', $paymentRef)->first();

            if ($paymentAttempt) {
                // Update the payment attempt status
                $paymentAttempt->status = strtolower($status); // 'success', 'failed', 'canceled'
                $paymentAttempt->save();

                if ($status === 'SUCCESS') {
                    Log::info("Payment reference {$paymentRef} for order {$orderId} was successful via webhook. Updating payment attempt and creating/updating user.");

                    // Retrieve user details from the stored payment attempt
                    $userEmail = $paymentAttempt->email;
                    $userFirstName = $paymentAttempt->first_name;
                    $userLastName = $paymentAttempt->last_name;
                    $userPhoneNumber = $paymentAttempt->phone_number;

                    if ($userEmail) {
                        $user = User::firstOrNew(['email' => $userEmail]);
                        $generatedPassword = null;

                        if (!$user->exists) {
                            $generatedPassword = Str::random(10);
                            $user->firstname = $userFirstName;
                            $user->name = $userLastName;
                            $user->phone = $userPhoneNumber;
                            $user->location = null;
                            $user->payment_getways = 'konnect';
                            $user->password = Hash::make($generatedPassword);
                            $user->niveau = null;
                            $user->role = 'client';
                            $user->language = 'fr';
                            $user->status = 1;
                            $user->path_photo = $this->randomAvatars[array_rand($this->randomAvatars)];

                            try {
                                $user->save();
                                Log::info('New user created via webhook after Konnect payment.', ['email' => $userEmail, 'user_id' => $user->id]);

                                Mail::to($user->email)->send(new UserWelcomeMail($user, $generatedPassword));
                                Log::info('Welcome email sent to new user via webhook.', ['email' => $user->email]);
                            } catch (\Exception $e) {
                                Log::error('Failed to create user or send welcome email via webhook.', [
                                    'email' => $userEmail,
                                    'error' => $e->getMessage(),
                                    'trace' => $e->getTraceAsString()
                                ]);
                            }
                        } else {
                            Log::info('Existing user found with email ' . $userEmail . '. Updating payment gateway via webhook.', ['user_id' => $user->id]);
                            $user->payment_getways = 'konnect';
                            $user->save();
                        }
                    } else {
                        Log::warning('Webhook: User email not found in payment attempt, skipping user creation/update.', ['paymentRef' => $paymentRef]);
                    }

                    // Fulfill order, grant course access, send confirmation email here
                    // e.g., $course = Course::find($paymentAttempt->course_id);
                    // if ($course) { $user->courses()->attach($course->id); }

                } elseif ($status === 'FAILED' || $status === 'CANCELED') {
                    Log::warning("Payment reference {$paymentRef} for order {$orderId} {$status} via webhook.");
                } else {
                    Log::info("Payment reference {$paymentRef} for order {$orderId} has status: {$status} via webhook. No action taken yet.");
                }
            } else {
                Log::warning('Konnect Webhook: Payment attempt not found for paymentRef: ' . $paymentRef, ['data' => $data]);
            }

            return response()->json(['status' => 'success']);
        }

        Log::warning('Konnect Webhook: Invalid data received or missing paymentRef/status.', $data);
        return response()->json(['status' => 'error', 'message' => 'Invalid webhook data'], 400);
    }
   /**
     * Handles the successful payment callback from Konnect.
     * This function now queries Konnect directly for the definitive status and creates/updates user.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function success(Request $request)
    {
        $konnectPaymentId = $request->query('payment_ref'); // Konnect's paymentRef from redirect
        $yourOrderId = $request->query('orderId'); // Your internal orderId from your successUrl

        Log::info('Payment success callback received.', ['request_params' => $request->all()]);

        if (!$konnectPaymentId) {
            Log::error('Payment success callback: Missing Konnect payment_ref in request query.', ['request_params' => $request->all()]);
            return view('payment.fail', ['message' => 'Détails de paiement Konnect manquants pour la page de succès.']);
        }

        $paymentAttempt = PaymentAttempt::where('konnect_payment_ref', $konnectPaymentId)->first();

        if (!$paymentAttempt && $yourOrderId) {
            $paymentAttempt = PaymentAttempt::where('order_id', $yourOrderId)->first();
             if ($paymentAttempt && empty($paymentAttempt->konnect_payment_ref)) {
                $paymentAttempt->konnect_payment_ref = $konnectPaymentId;
                $paymentAttempt->save();
             }
        }

        if (!$paymentAttempt) {
            Log::error('Payment success callback: No matching payment attempt found in DB for Konnect ID: ' . $konnectPaymentId . ' and your order ID: ' . $yourOrderId);
            return view('payment.fail', [
                'message' => 'Impossible de trouver les détails de votre paiement dans notre système. Veuillez contacter le support.',
                'paymentDetails' => ['id' => $konnectPaymentId, 'status' => 'unknown_local_data_missing']
            ]);
        }

        // Query Konnect's API directly to get the latest status
        $konnectDetails = $this->getPaymentDetails($konnectPaymentId);

        if ($konnectDetails && $konnectDetails['status'] === 'completed') { // Konnect API returns 'completed' for success
            // Update the local payment attempt status to success
            $paymentAttempt->status = 'success';
            $paymentAttempt->save();

            Log::info('Payment success callback confirmed via Konnect API. Processing user creation/update and email.', [
                'konnect_payment_id' => $konnectPaymentId,
                'your_order_id' => $paymentAttempt->order_id,
                'konnect_status' => $konnectDetails['status']
            ]);

            // Retrieve user details from the stored payment attempt
            $userEmail = $paymentAttempt->email;
            $userFirstName = $paymentAttempt->first_name  ?? 'Client';
            $userLastName = $paymentAttempt->last_name ?? 'Client';
            $userPhoneNumber = $paymentAttempt->phone_number;

            if ($userEmail) {
                $user = User::firstOrNew(['email' => $userEmail]);
                $generatedPassword = null;

                if (!$user->exists) {
                    $generatedPassword = Str::random(10);
                    $user->firstname = $userFirstName;
                    $user->name = $userLastName;
                    $user->phone = $userPhoneNumber;
                    $user->location = null;
                    $user->payment_getways = 'konnect';
                    $user->password = Hash::make($generatedPassword);
                    $user->niveau = null;
                    $user->role = 'client';
                    $user->language = 'fr';
                    $user->status = 1;
                    $user->path_photo = $this->randomAvatars[array_rand($this->randomAvatars)];

                    try {
                        $user->save();
                        Log::info('New user created after Konnect payment success redirect.', ['email' => $userEmail, 'user_id' => $user->id]);
                        Mail::to($user->email)->send(new UserWelcomeMail($user, $generatedPassword));
                        Log::info('Welcome email sent to new user after Konnect payment success redirect.', ['email' => $user->email]);
                    } catch (\Exception $e) {
                        Log::error('Failed to create user or send welcome email after Konnect payment success redirect.', [
                            'email' => $userEmail,
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                    }
                } else {
                    Log::info('Existing user found with email ' . $userEmail . '. Updating payment gateway after Konnect payment success redirect.', ['user_id' => $user->id]);
                    $user->payment_getways = 'konnect';
                    $user->save();
                }
                $update_payment = New Payments();
                $update_payment->methode = 'konnect';
                $update_payment->img_path = null;
                $update_payment->online_key = $konnectPaymentId;
                $update_payment->status = 1;
                $update_payment->user_id = $user->id;
                $update_payment->save();
            } else {
                Log::warning('Success redirect: User email not found in payment attempt, skipping user creation/update.', ['paymentRef' => $konnectPaymentId]);
            }

            // Fulfill order, grant course access, send confirmation email here
            // e.g., $course = Course::find($paymentAttempt->course_id);
            // if ($course) { $user->courses()->attach($course->id); }

            return view('payment.success', [
                'message' => 'Votre paiement a été un succès!',
                'paymentDetails' => [
                    'id' => $konnectPaymentId,
                    'status' => $konnectDetails['status'],
                    'orderId' => $paymentAttempt->order_id,
                    'amount' => $paymentAttempt->amount,
                    'email' => $paymentAttempt->email,
                    'phoneNumber' => $paymentAttempt->phone_number,
                    'firstName' => $paymentAttempt->first_name,
                    'lastName' => $paymentAttempt->last_name,
                ],
            ]);
        } else {
            // If Konnect details are not 'completed', treat it as a failure on the success page
            $paymentAttempt->status = strtolower($konnectDetails['status'] ?? 'failed'); // Default to 'failed' if status is missing
            $paymentAttempt->save();
            Log::warning('Payment success callback: Konnect API did not confirm success, redirecting to fail page.', [
                'konnect_payment_id' => $konnectPaymentId,
                'konnect_status' => $konnectDetails['status'] ?? 'N/A'
            ]);
            return redirect()->route('payment.fail', [
                'payment_ref' => $konnectPaymentId,
                'orderId' => $yourOrderId
            ]);
        }
    }
    /**
     * Handles the successful payment callback from Konnect.
     * It retrieves payment details to verify the status.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function success_validator(Request $request)
    {
        $paymentId = $request->query('payment_ref');

        if (!$paymentId) {
            Log::error('Payment success callback: No paymentId found in request.', ['request_params' => $request->all()]);
            return view('payment.fail', ['message' => 'Payment ID not found.']);
        }

        $paymentDetailsResponse = $this->getPaymentDetails($paymentId);

        if ($paymentDetailsResponse) {
            // Log the payment details for debugging
            Log::info('Successful payment details retrieved:', ['payment_id' => $paymentId, 'details' => $paymentDetailsResponse]);

            // Konnect API response structure: $paymentDetailsResponse is the 'payment' object
            if (isset($paymentDetailsResponse['status']) && $paymentDetailsResponse['status'] === 'completed') {
                // Payment was successfully completed

                // 1. Create or update user
                $userEmail = $paymentDetailsResponse['email'] ?? null;
                $userPhone = $paymentDetailsResponse['phoneNumber'] ?? null;

                if ($userEmail) {
                    $user = User::firstOrNew(['email' => $userEmail]);

                    // Only set these attributes if the user is new OR if you want to update them on existing users
                    if (!$user->exists) {
                        $user->firstname = 'Client';
                        $user->name = 'Client';
                        $user->phone = $userPhone;
                        $user->location = null;
                        $user->payment_getways = 'konnect';
                        $user->password = Hash::make(Str::random(10)); // Generate a random 10-character password
                        $user->niveau = null;
                        $user->role = 'client';
                        $user->language = 'fr';
                        $user->status = 1;
                        $user->path_photo = $this->randomAvatars[array_rand($this->randomAvatars)];
                        $user->save();
                        Log::info('New user created upon successful Konnect payment.', ['email' => $userEmail, 'user_id' => $user->id]);

                        try {
                            Mail::to($user->email)->send(new UserWelcomeMail($user, $generatedPassword));
                            Log::info('Welcome email sent to new user.', ['email' => $user->email]);
                        } catch (\Exception $e) {
                            Log::error('Failed to send welcome email to new user.', ['email' => $user->email, 'error' => $e->getMessage()]);
                        }
                    } else {
                        Log::info('Existing user found with email ' . $userEmail . '. Not creating a new user.');
                        // You might want to update some fields for existing users, e.g., last_payment_gateway
                        $user->payment_getways = 'konnect';
                        $user->save();
                    }
                } else {
                    Log::warning('Konnect payment success: User email not found in payment details, skipping user creation.', ['paymentDetails' => $paymentDetailsResponse]);
                }


                // 2. Mark the order as paid in your database.
                // 3. Grant access to the course (using $paymentDetailsResponse['orderId'] to link to your internal order)
                // 4. Send a confirmation email to the user.
                // 5. Any other business logic related to a successful payment.

                return view('payment.success', [
                    'message' => 'Votre paiement a été un succès!', // Updated message to French
                    'paymentDetails' => $paymentDetailsResponse,
                ]);
            } else {
                // Payment status is not 'completed' despite being on the success URL
                Log::warning('Payment callback hit success URL but status is not completed.', [
                    'payment_id' => $paymentId,
                    'status' => $paymentDetailsResponse['status'] ?? 'N/A'
                ]);
                return view('payment.fail', [
                    'message' => 'Le statut du paiement n\'est pas confirmé comme réussi. Veuillez vérifier votre compte ou contacter le support.',
                    'paymentDetails' => $paymentDetailsResponse,
                ]);
            }
        } else {
            Log::error('Payment success callback: Failed to retrieve payment details for ID: ' . $paymentId);
            return view('payment.fail', ['message' => 'Impossible de vérifier le statut du paiement. Veuillez contacter le support.']);
        }
    }

    /**
     * Handles the failed payment callback from Konnect.
     * It still retrieves payment details to verify the status, as this page needs to show a reason.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function fail(Request $request)
    {
        $konnectPaymentId = $request->query('payment_ref'); // Konnect's paymentRef from the redirect

        $message = 'Votre paiement a échoué ou a été annulé.';
        $paymentDetails = null;

        if ($konnectPaymentId) {
            // For a failed payment, it's good to confirm the status directly
            // from Konnect to show the user the correct reason.
            $paymentDetailsResponse = $this->getPaymentDetails($konnectPaymentId);

            // Also, update your local payment attempt record
            $paymentAttempt = PaymentAttempt::where('konnect_payment_ref', $konnectPaymentId)->first();
            if ($paymentAttempt) {
                $paymentAttempt->status = 'failed'; // Or 'canceled', based on Konnect's response
                $paymentAttempt->save();
                Log::info('Payment attempt updated to failed via failUrl.', ['konnect_payment_ref' => $konnectPaymentId]);
            } else {
                Log::warning('Payment fail callback: No payment attempt found in DB for Konnect paymentId: ' . $konnectPaymentId);
            }

            if ($paymentDetailsResponse) {
                Log::info('Failed payment details retrieved:', ['payment_id' => $konnectPaymentId, 'details' => $paymentDetailsResponse]);

                if (isset($paymentDetailsResponse['status'])) {
                    $message = 'Votre paiement a échoué. Statut actuel : ' . $paymentDetailsResponse['status'] . '.';
                }
                $paymentDetails = $paymentDetailsResponse;
            } else {
                Log::error('Payment fail callback: Failed to retrieve payment details for ID: ' . $konnectPaymentId);
                $message .= ' Impossible de récupérer des détails spécifiques.';
            }
        } else {
            Log::warning('Payment fail callback: No paymentId found in request.', ['request_params' => $request->all()]);
            $message .= ' Aucun identifiant de paiement spécifique fourni.';
        }

        return view('payment.fail', [
            'message' => $message,
            'paymentDetails' => $paymentDetails,
        ]);
    }

    private function getPaymentDetails(string $paymentId): ?array
    {
        $client = new Client();
        try {
            $response = $client->get("{$this->konnectBaseUrl}payments/{$paymentId}", [
                'headers' => [
                    'x-api-key' => $this->konnectApiKey,
                    'Content-Type' => 'application/json',
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (isset($data['payment'])) {
                return $data['payment'];
            }

            Log::error('Konnect Get Payment Details API: "payment" key missing in response.', ['response' => $data]);
            return null;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            $statusCode = $response->getStatusCode();
            $responseBody = $response->getBody()->getContents();
            Log::error("Konnect Get Payment Details API Client Error ({$statusCode}): " . $responseBody, ['payment_id' => $paymentId, 'exception' => $e]);
            return null;
        } catch (\Exception $e) {
            Log::error('Konnect Get Payment Details API Error: ' . $e->getMessage(), ['payment_id' => $paymentId, 'exception' => $e]);
            return null;
        }
    }

    public function handleManualPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_method' => 'required|in:virement_bancaire,d17,espece',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'course_id' => 'required|integer|exists:cours,id',
            'proof_file' => ($request->input('payment_method') !== 'espece' ? 'required' : 'nullable') . '|file|mimes:jpeg,jpg,png,pdf,webp|max:2048'
        ], [
            'proof_file.required' => 'Une preuve de paiement est requise pour cette méthode.'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        // 1. Trouver ou créer l'utilisateur
        $generatedPassword = Str::random(10);
        $user = User::firstOrCreate(
            ['email' => $request->input('email')],
            [
                'name' => 'Utilisateur',
                'firstname' => 'Nouveau',
                'payment_getways' => $request->input('payment_method'),
                'phone' => $request->input('phone'),
                'password' => Hash::make($generatedPassword),
                'role' => 'client',
                'status' => 1,
                'path_photo' => $this->randomAvatars[array_rand($this->randomAvatars)],
            ]
        );
        
        $filePath = null;
        $originalName = null;
        $filename = null;
        $mimeType = null;
        $size = null;

        // 2. Gérer l'upload du fichier si présent
        if ($request->hasFile('proof_file')) {
            $file = $request->file('proof_file');
            $paymentMethod = $request->input('payment_method');
            
            // Générer un nom de fichier unique
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            
            // Stocker le fichier dans 'storage/app/public/proofs/{methode}'
            $filePath = $file->storeAs("proofs/{$paymentMethod}", $filename, 'public');

            // Récupérer les informations du fichier
            $originalName = $file->getClientOriginalName();
            $mimeType = $file->getMimeType();
            $size = $file->getSize();
        }

        try {
            // 3. Créer une entrée dans la table 'payments'
            $payment = Payments::create([
                'user_id' => $user->id,
                'methode' => $request->input('payment_method'),
                'img_path' => 'storage/'.$filePath, // Stocke le chemin du fichier ici
                'online_key' => null, // Pas de clé pour les paiements manuels
                'status' => 0, // 0 pour 'en attente', 1 pour 'approuvé'
            ]);

            // 4. Créer une entrée dans la table 'uploaded_files' si un fichier a été envoyé
            if ($filePath) {
                UploadedFile::create([
                    'file_id' => (string) Str::uuid(), // Générer un ID unique pour le fichier
                    'original_name' => $originalName,
                    'filename' => $filename,
                    'path' => $filePath,
                    'mime_type' => $mimeType,
                    'size' => $size,
                    'payment_method' => $request->input('payment_method'),
                    'user_id' => $user->id,
                    'course_id' => $request->input('course_id'),
                    'status' => 'pending',
                    'notes' => 'Soumis par l\'utilisateur pour vérification.'
                ]);
            }

            // Vous pouvez également envoyer une notification à un admin ici

            return response()->json([
                'success' => true,
                'message' => 'Votre demande de paiement a été soumise. Elle sera examinée par notre équipe sous peu.'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la sauvegarde du paiement manuel: ' . $e->getMessage());
            // En cas d'erreur, supprimer le fichier qui a pu être uploadé
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
            return response()->json(['message' => 'Une erreur interne est survenue lors de la soumission.'], 500);
        }
    }
}