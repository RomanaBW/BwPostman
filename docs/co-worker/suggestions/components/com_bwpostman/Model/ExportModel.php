<?php

/**
 * BwPostman component
 *
 * Site class of the BwPostman component.
 *
 * @version %%version_number%%
 * @package BwPostman-Site
 * @author Romana Boldt
 * @copyright (C) %%copyright_year%% Boldt Webservice <forum@boldt-webservice.de>
 * @support https://www.boldt-webservice.de/en/forum-en/forum/bwpostman.html
 * @license GNU/GPL, see LICENCE.txt
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public Licence as published by
 * the Free Software Foundation, either version 3 of the Licence, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public Licence for more details.
 *
 * You should have received a copy of the GNU General Public Licence
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace \BoldtWebservice\Component\BwPostman\Site\Model;

use \BoldtWebservice\Component\BwPostman\Administrator\Service\BwFilesystemServiceAwareInterface;
use \BoldtWebservice\Component\BwPostman\Administrator\Service\BwFilesystemServiceAwareTrait;
use \BoldtWebservice\Component\BwPostman\Administrator\Service\BwLoggerServiceAwareInterface;
use \BoldtWebservice\Component\BwPostman\Administrator\Service\BwLoggerServiceAwareTrait;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

/**
 * Site example model.
 *
 * Shows how the Site part imports the services from the Administrator
 * namespace (both prefixes are autoloadable system-wide) – DRY, no second
 * Service folder required.
 *
 * SECURITY: ACL checks belong in the Site controller, NEVER solely in
 * shared services.
 *
 * @since   4.4.0
 */
class ExportModel extends BaseDatabaseModel implements
    BwFilesystemServiceAwareInterface,
    BwLoggerServiceAwareInterface
{
    use BwFilesystemServiceAwareTrait;
    use BwLoggerServiceAwareTrait;

    /**
     * Returns the size of a file.
     *
     * @param   string  $file  Path to the file.
     *
     * @return  integer|null  File size in bytes, null if the file does not exist.
     *
     * @since   4.4.0
     */
    public function fileSize(string $file): ?int
    {
        if (!$this->filesystemService->exists($file)) {
            $this->loggerService->error('File not found', ['file' => $file]);

            return null;
        }

        return \filesize($file) ?: null;
    }
}