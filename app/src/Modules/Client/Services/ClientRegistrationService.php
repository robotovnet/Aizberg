<?php

declare(strict_types=1);

namespace Modules\Client\Services;

use Modules\Client\Repositories\ClientRepository;
use Illuminate\Support\Facades\Config;
use Nwidart\Modules\Facades\Module;
use App;

/**
 * Class ClientRegistrationService
 * @package Modules\Client\Services
 */
class ClientRegistrationService
{
    /** @var string Where to redirect a new client after registration. */
    private string $redirectPath;

    /** @var array|string[] List of attached modules when registering. */
    private array $regModules;

    /** @var ClientRepository An instance of the Client Repository. */
    private ClientRepository $clientRepository;

    /**
     * ClientRegistrationService constructor.
     */
    public function __construct(ClientRepository $clientRepository)
    {
        $moduleName = Module::getModuleName(__NAMESPACE__);

        $this->redirectPath = Config::get($moduleName . '.redirect_path_after_registration');
        $this->regModules   = Module::getModuleNames(Config::get($moduleName . '.registration_modules'));

        $this->clientRepository = $clientRepository;
    }

    /**
     * Returns the redirection path, where to redirect a new customer after registration.
     */
    public function getRedirectPath(): string
    {
        return $this->redirectPath;
    }

    /**
     * Create a new client.
     *
     * @param array $data Registration data
     */
    public function createNew(array $data): int
    {
        $clientId = $this->clientRepository->create($data);
        $this->attachEntities($data);

        return $clientId;
    }

    /**
     * Attaches entities to the client.
     *
     * @param  array  $data Registration data
     */
    private function attachEntities(array $data): void
    {
        foreach ($this->regModules as $moduleName) {
            if (Module::has($moduleName)) {
                $attachService = Module::getClassName(
                    Module::getModulePath($moduleName),
                    'Services',
                    $moduleName . 'AttachService'
                );

                $instance = App::make($attachService);
                $this->clientRepository->attach($moduleName, $instance->getEntityId($data));
            }
        }
    }
}
