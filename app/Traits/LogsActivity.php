<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait LogsActivity
{
    /**
     * Log a create action
     */
    protected function logCreate($model, $description = null)
    {
        $modelName = class_basename($model);
        $description = $description ?? "Menambahkan {$modelName}: {$this->getModelIdentifier($model)}";

        ActivityLog::log('create', $description, $model, null, $model->toArray());
    }

    /**
     * Log an update action
     */
    protected function logUpdate($model, $oldValues, $description = null)
    {
        $modelName = class_basename($model);
        $description = $description ?? "Mengubah {$modelName}: {$this->getModelIdentifier($model)}";

        ActivityLog::log('update', $description, $model, $oldValues, $model->toArray());
    }

    /**
     * Log a delete action
     */
    protected function logDelete($model, $description = null)
    {
        $modelName = class_basename($model);
        $description = $description ?? "Menghapus {$modelName}: {$this->getModelIdentifier($model)}";

        ActivityLog::log('delete', $description, $model, $model->toArray(), null);
    }

    /**
     * Log a custom action
     */
    protected function logActivity($action, $description, $model = null)
    {
        ActivityLog::log($action, $description, $model);
    }

    /**
     * Get identifier for model (title, name, or id)
     */
    private function getModelIdentifier($model)
    {
        return $model->title ?? $model->name ?? $model->id;
    }
}
