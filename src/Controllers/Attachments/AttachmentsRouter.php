<?php declare(strict_types=1);

namespace Reconmap\Controllers\Attachments;

use League\Route\RouteCollectionInterface;

class AttachmentsRouter
{
    public function mapRoutes(RouteCollectionInterface $router): void
    {
        $router->map('GET', '/attachments', GetAttachmentsController::class);
        $router->map('POST', '/attachments', UploadAttachmentController::class);
        $router->map('DELETE', '/attachments/{attachmentId:number}', DeleteAttachmentController::class);
    }
}
