<?php
/*
 * Copyright 2015-present MongoDB, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace MongoDB;

use MongoDB\Driver\Exception\LogicException;
use MongoDB\Driver\WriteResult;

/**
 * Result class for a delete operation.
 */
class DeleteResult
{
    public function __construct(private WriteResult $writeResult)
    {
    }

    /**
     * Return the number of documents that were deleted.
     *
     * This method should only be called if the write was acknowledged.
     *
     * @see DeleteResult::isAcknowledged()
     * @throws LogicException if the write result is unacknowledged
     */
    public function getDeletedCount(): int
    {
        return $this->writeResult->getDeletedCount();
    }

    /**
     * Return whether this delete was acknowledged by the server.
     *
     * If the delete was not acknowledged, other fields from the WriteResult
     * (e.g. deletedCount) will be undefined.
     */
    public function isAcknowledged(): bool
    {
        return $this->writeResult->isAcknowledged();
    }
}
