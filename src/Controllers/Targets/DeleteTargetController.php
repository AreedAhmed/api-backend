<?php declare(strict_types=1);

namespace Reconmap\Controllers\Targets;

use Psr\Http\Message\ServerRequestInterface;
use Reconmap\Controllers\Controller;
use Reconmap\Models\AuditLogAction;
use Reconmap\Repositories\TargetRepository;
use Reconmap\Services\AuditLogService;

class DeleteTargetController extends Controller
{

    public function __invoke(ServerRequestInterface $request, array $args): array
    {
        $targetId = (int)$args['targetId'];

        $repository = new TargetRepository($this->db);
        $success = $repository->deleteById($targetId);

        $userId = $request->getAttribute('userId');
        $this->auditAction($userId, $targetId);

        return ['success' => $success];
    }

    private function auditAction(int $loggedInUserId, int $targetId): void
    {
        $auditLogService = new AuditLogService($this->db);
        $auditLogService->insert($loggedInUserId, AuditLogAction::TARGET_DELETED, ['type' => 'target', 'id' => $targetId]);
    }
}
