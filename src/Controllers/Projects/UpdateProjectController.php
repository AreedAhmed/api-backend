<?php declare(strict_types=1);

namespace Reconmap\Controllers\Projects;

use Psr\Http\Message\ServerRequestInterface;
use Reconmap\Controllers\Controller;
use Reconmap\Models\AuditLogAction;
use Reconmap\Repositories\ProjectRepository;
use Reconmap\Services\ActivityPublisherService;

class UpdateProjectController extends Controller
{
    public function __invoke(ServerRequestInterface $request, array $args): array
    {
        $projectId = (int)$args['projectId'];

        $requestBody = $this->getJsonBodyDecodedAsArray($request);
        $newColumnValues = array_filter(
            $requestBody,
            fn(string $key) => in_array($key, array_keys(ProjectRepository::UPDATABLE_COLUMNS_TYPES)),
            ARRAY_FILTER_USE_KEY
        );

        $success = false;
        if (!empty($newColumnValues)) {
            $repository = new ProjectRepository($this->db);
            $this->replaceEmptyWithNulls(['engagement_start_date', 'engagement_end_date'], $newColumnValues);
            $success = $repository->updateById($projectId, $newColumnValues);

            $loggedInUserId = $request->getAttribute('userId');
            $this->auditAction($loggedInUserId, $projectId);
        }

        return ['success' => $success];
    }

    private function auditAction(int $loggedInUserId, int $projectId): void
    {
        $activityPublisherService = $this->container->get(ActivityPublisherService::class);
        $activityPublisherService->publish($loggedInUserId, AuditLogAction::PROJECT_MODIFIED, ['projectId' => $projectId]);
    }

    private function replaceEmptyWithNulls(array $columns, array &$columnValues): void
    {
        foreach ($columns as $column) {
            if (array_key_exists($column, $columnValues) && $columnValues[$column] === '') {
                $columnValues[$column] = null;
            }
        }
    }
}
