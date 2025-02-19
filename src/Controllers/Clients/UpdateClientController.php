<?php declare(strict_types=1);

namespace Reconmap\Controllers\Clients;

use Psr\Http\Message\ServerRequestInterface;
use Reconmap\Controllers\Controller;
use Reconmap\Models\AuditLogAction;
use Reconmap\Repositories\ClientRepository;
use Reconmap\Services\ActivityPublisherService;

class UpdateClientController extends Controller
{
    public function __invoke(ServerRequestInterface $request, array $args): array
    {
        $clientId = (int)$args['clientId'];

        $requestBody = $this->getJsonBodyDecodedAsArray($request);
        $newColumnValues = array_filter(
            $requestBody,
            fn(string $key) => in_array($key, array_keys(ClientRepository::UPDATABLE_COLUMNS_TYPES)),
            ARRAY_FILTER_USE_KEY
        );

        $success = false;
        if (!empty($newColumnValues)) {
            $repository = new ClientRepository($this->db);
            $success = $repository->updateById($clientId, $newColumnValues);

            $loggedInUserId = $request->getAttribute('userId');
            $this->auditAction($loggedInUserId, $clientId);
        }

        return ['success' => $success];
    }

    private function auditAction(int $loggedInUserId, int $clientId): void
    {
        $activityPublisherService = $this->container->get(ActivityPublisherService::class);
        $activityPublisherService->publish($loggedInUserId, AuditLogAction::CLIENT_MODIFIED, ['clientId' => $clientId]);
    }
}
