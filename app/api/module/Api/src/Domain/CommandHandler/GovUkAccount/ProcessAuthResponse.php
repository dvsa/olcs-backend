<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Domain\CommandHandler\GovUkAccount;

use Dvsa\GovUkAccount\Provider\GovUkAccountUser;
use Dvsa\Olcs\Api\Domain\Command\DigitalSignature\UpdateApplication;
use Dvsa\Olcs\Api\Domain\Command\DigitalSignature\UpdateContinuationDetail;
use Dvsa\Olcs\Api\Domain\Command\DigitalSignature\UpdateSurrender;
use Dvsa\Olcs\Api\Domain\Command\DigitalSignature\UpdateTmApplication;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Service\GovUkAccount\Data\Attributes;
use Dvsa\Olcs\Api\Service\GovUkAccount\GovUkAccountService;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Transfer\Command\GovUkAccount\ProcessAuthResponse as ProcessAuthResponseCmd;

class ProcessAuthResponse extends AbstractCommandHandler implements TransactionedInterface
{
    const ERR_MISSING_KEY_CLAIM = 'Unable to verify user identity OR key claims core identity does not exist';
    const ERR_MISSING_JOURNEY = 'Journey not matched or not specified';
    const ERR_INSUFFICIENT_TRUST = 'We require %s level of trust, actual level of trust was %s';

    protected $repoServiceName = 'DigitalSignature';

    private GovUkAccountService $govUkAccountService;

    public function __construct(GovUkAccountService $govUkAccountService)
    {
        $this->govUkAccountService = $govUkAccountService;
    }

    public function handleCommand(CommandInterface $command): Result
    {
        assert($command instanceof ProcessAuthResponseCmd);

        $code = $command->getCode();
        $token = $command->getState();

        $stateTokenClaims = $this->govUkAccountService->getStateClaimsFromToken($token);
        $accessToken = $this->govUkAccountService->getAccessToken($code);
        $userDetails = $this->govUkAccountService->getUserDetails($accessToken)->toArray();

        if (!isset($userDetails[GovUkAccountUser::KEY_CLAIMS_CORE_IDENTITY_DECODED])) {
            throw new \Exception(self::ERR_MISSING_KEY_CLAIM);
        }

        $vot = $userDetails[GovUkAccountUser::KEY_CLAIMS_CORE_IDENTITY_DECODED]['vot'];

        $meetsVectorOfTrust = $this->govUkAccountService->meetsVectorOfTrust($vot, GovUkAccountService::VOT_P2);

        if (!$meetsVectorOfTrust) {
            $message = sprintf(self::ERR_INSUFFICIENT_TRUST, GovUkAccountService::VOT_P2, $vot);
            throw new \Exception($message);
        }

        $userAttributes = $userDetails[GovUkAccountUser::KEY_CLAIMS_CORE_IDENTITY_DECODED]['vc']['credentialSubject'];
        $name = $this->govUkAccountService->processNames($userAttributes['name']);

        //this is done to match the format of the previous govuk verify
        $attributes = new Attributes(
            [
                Attributes::FIRST_NAME => $name['firstName'] ?? null,
                Attributes::SURNAME => $name['familyName'] ?? null,
                Attributes::DATE_OF_BIRTH => $userAttributes['birthDate'][0]['value'] ?? null,
            ]
        );

        $digitalSignature = new Entity\DigitalSignature();
        $digitalSignature->addSignatureInfo($attributes, $userDetails[GovUkAccountUser::KEY_CLAIMS_CORE_IDENTITY]);
        $this->getRepo()->save($digitalSignature);

        $digitalSignatureId = $digitalSignature->getId();

        $this->result->addId('DigitalSignature', $digitalSignatureId);
        $this->result->addMessage('Digital signature created');

        switch ($stateTokenClaims['journey']) {
            case RefData::JOURNEY_NEW_APPLICATION:
                $sideEffectCmd = UpdateApplication::create(
                    [
                        'application' => $stateTokenClaims['id'],
                        'digitalSignature' => $digitalSignatureId,
                    ]
                );
                break;
            case RefData::JOURNEY_CONTINUATION:
                $sideEffectCmd = UpdateContinuationDetail::create(
                    [
                        'continuationDetail' => $stateTokenClaims['id'],
                        'digitalSignature' => $digitalSignatureId,
                    ]
                );
                break;
            case RefData::JOURNEY_SURRENDER:
                $sideEffectCmd = UpdateSurrender::create(
                    [
                        'licence' => $stateTokenClaims['id'],
                        'digitalSignature' => $digitalSignatureId,
                    ]
                );
                break;
            case RefData::JOURNEY_TM_APPLICATION:
                $sideEffectCmd = UpdateTmApplication::create(
                    [
                        'application' => $stateTokenClaims['id'],
                        'digitalSignature' => $digitalSignatureId,
                        'role' => $stateTokenClaims['role'],
                    ]
                );
                break;
            default:
                throw new \Exception(self::ERR_MISSING_JOURNEY);
        }

        $this->result->merge(
            $this->handleSideEffect($sideEffectCmd)
        );

        //pass back the state token return url
        $this->result->setFlag('redirect_url', $stateTokenClaims['returnUrl']);

        return $this->result;
    }
}
