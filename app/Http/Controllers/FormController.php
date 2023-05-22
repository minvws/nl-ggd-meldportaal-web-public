<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ContactFormRequest;
use App\Models\User;
use App\Role;
use App\Services\Postcode\PostcodeService;
use App\Services\TestService;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use MinVWS\Crypto\Laravel\Factory;
use MinVWS\Crypto\Laravel\SealboxCryptoInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

class FormController extends BaseController
{
    protected PostcodeService $postcodeService;
    protected TestService $testService;

    public function __construct(PostcodeService $postcodeService, TestService $testService)
    {
        $this->postcodeService = $postcodeService;
        $this->testService = $testService;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('form.index')->with('backend_public_key', config('crypto.backend.public_key'));
    }

    public function address(Request $request): JsonResponse
    {
        if ($this->postcodeService->isEnabled() === false) {
            return response()->json(['error' => 'Postcode service is disabled'], 503);
        }

        $encryptedPostcode = $request->get('pc');
        $encryptedHouseNumber = $request->get('hn');
        $encryptionKey = strval(hex2bin($request->get('key')));

        // Decode postcode with out own backend key
        $sealbox = Factory::createSealboxCryptoService(
            privKey: config('crypto.backend.private_key'),
            recipientPubKey: config('crypto.backend.public_key')
        );

        $postcode = $sealbox->decrypt(base64_decode($encryptedPostcode));
        $postcode = strtoupper($postcode);
        $postcode = preg_replace('/\s+/', '', $postcode);

        $houseNumber = intval($sealbox->decrypt(base64_decode($encryptedHouseNumber)));

        $info = $this->postcodeService->resolve($postcode ?? '', $houseNumber, 'nl');
        if ($info === null) {
            return response()->json(['error' => 'Postcode not found'], 404);
        }

        // Encrypt output data with $encryptionKey from the frontend
        $jsonInfo = strval(json_encode($info));
        $sealbox = Factory::createSealboxCryptoService(
            privKey: base64_encode($encryptionKey),
            recipientPubKey: base64_encode($encryptionKey)
        );
        $encryptedJsonInfo = $sealbox->encrypt($jsonInfo);
        return response()->json(['data' => base64_encode($encryptedJsonInfo)]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse|Response|void
     * @throws \Illuminate\Validation\ValidationException
     */
    public function encryptedStore(Request $request)
    {
        $sealbox = Factory::createSealboxCryptoService(
            privKey: config('crypto.backend.private_key'),
            recipientPubKey: config('crypto.backend.public_key')
        );

        $encryptedFormData = $request->get('formdata');
        $confirmed = $request->get('confirmed', false);

        // Decrypt the formdata blob and inject it into the request so we can validate it.
        $formData = $this->decryptFormData($request->get('formdata') ?? '', $sealbox);

        // Convert date_of_sample_collection to a valid date format with time
        $formData['data']['date_of_sample_collection'] =
            $formData['data']['date_of_sample_collection'] . 'T' . $formData['data']['date_of_sample_collection_time']
        ;
        unset($formData['data']['date_of_sample_collection_time']);
        $request->request->replace($formData);

        $formRequest = new ContactFormRequest(request: $request->all());
        $validator = Validator::make(
            $request->all(),
            rules: $formRequest->rules(),
            messages: $formRequest->messages(),
            customAttributes: $formRequest->attributes()
        );
        if (!$validator->passes()) {
            return back()->withErrors($validator);
        }


        // If the verified flag is not set, we display the confirmation page.
        if (! $confirmed) {
            return view('form.confirm')
                ->with('backend_public_key', config('crypto.backend.public_key'))
                ->with('formdata', $encryptedFormData)
            ;
        }

        /** @var User $user */
        $user = Auth::user();

        // Data is verified, and confirmed by the user, so we can process the data.
        if (!$this->testService->storeTest($validator->valid()['data'] ?? [], $user, $user->hasRole(Role::SPECIMEN))) {
            throw new HttpException(500, 'Something went wrong');
        }

        return view('form.confirmed');
    }

    /**
     * @param ContactFormRequest $request
     * @return void
     * @throws \Exception
     */
    public function store(ContactFormRequest $request)
    {
        throw new \Exception("Not implemented yet");
    }

    protected function decryptFormData(string $encryptedFormData, SealboxCryptoInterface $sealbox): array
    {
        $decryptedFormData = $sealbox->decrypt(base64_decode($encryptedFormData));
        $formData = json_decode($decryptedFormData, true);
        if (!$formData) {
            $formData = [];
        }

        // Data keys are in "foo[bar]" string format, so we need to convert it to an array format.
        // We can use parse_str for this, but we have to recreate the structure for parse_str to work.
        $s = "";
        foreach ($formData as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $v1) {
                    $s .= "$k=$v1&";
                }
            } else {
                $s .= "$k=$v&";
            }
        }

        parse_str($s, $result);
        return $result;
    }
}
