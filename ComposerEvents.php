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

        $p=$event->getComposer()->getPackage()->getRequires();
        if (!isset($p['sypexgeo/sypexgeo'])) {
             $event->getIO()->write('<error>sypexgeo is not required!</error>');
             return;
        }
        $targetDir = realpath(dirname(__DIR__).'/../sypexgeo/sypexgeo');

        foreach ($extra['sxgeo-databases'] as $database) {
            if (!isset($databases[$database])) {
                $event->getIO()->write(sprintf('<error>Unknown database "%s"</error>', $database));
            }

            $zipfile = $targetDir . '/' . basename($databases[$database]);
            $datfile=$targetDir . '/' . $database.'.dat';
            if (is_file($datfile)) {
                continue;
            }
            $event->getIO()->write(sprintf('Installing "%s" database', $database));

            copy($databases[$database], $zipfile);

            $zip = new ZipArchive;
            $res = $zip->open($zipfile);
            if ($res === TRUE) {
                if ($zip->extractTo($targetDir,$database.'.dat')){
                    $event->getIO()->write(sprintf('"%s" extracted', $database));
                } else {
                    $event->getIO()->write(sprintf('<error>Error extracting file "%s"</error>',$database));
                }
                $zip->close();
                unlink($zipfile);
            } else {
                $event->getIO()->write(sprintf('<error>Can\'t unzip file "%s"</error>', basename($zipfile)));
            }
        }
    }
}
