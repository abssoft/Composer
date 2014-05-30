<?php


use Composer\Script\Event;

/**
 * Class ComposerEvents
 *
 * Example
 * <code>
 *   "scripts": {
 *     "post-install-cmd": [
 *     "ComposerEvents::SxGeo",
 *   },
 *   "extra": {
 *     "sxgeo-databases": [
 *        "SxGeo",
 *        "SxGeoCity"
 *     ]
 *   }
 * </code>
 */
class ComposerEvents
{
    public static function SxGeo(Event $event)
    {
        $extra = $event->getComposer()->getPackage()->getExtra();

        if (empty($extra['sxgeo-databases'])) {
            $event->getIO()->write('<warning>No databases to install</warning>');
        }

        $databases = array(
            'SxGeoCity' => 'http://sypexgeo.net/files/SxGeoCity_utf8.zip',
        );

        $targetDir = dirname(__DIR__);

        foreach ($extra['sxgeo-databases'] as $database) {
            if (!isset($databases[$database])) {
                $event->getIO()->write(sprintf('<error>Unknown database "%s"</error>', $database));
            }

			      $zipfile = $targetDir . '/' . basename($databases[$database]);
			      $datfile=$targetDir . '/' . basename($databases[$database],'.zip').'.dat';
            if (is_file($datfile)) {
                continue;
            }
            $event->getIO()->write(sprintf('Installing "%s" database', $database));

            copy($databases[$database], $zipfile);
            $zip = new ZipArchive;
            $res = $zip->open($zipfile, ZIPARCHIVE::OVERWRITE);
            if ($res === TRUE) {
              $zip->extractTo('.');
              $zip->close();
              unlink($zipfile);
            } else {
              $event->getIO()->write(sprintf('<error>Can\'t unzip file "%s"</error>', basename($zipfile)));
            }

            
        }
    }
}
