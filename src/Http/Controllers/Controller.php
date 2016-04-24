<?php
namespace Jihe\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Jihe\Http\Presenters\JsonPresenter;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 *  Base controller that is supposed to be sub-classed by all controller within this application
 */
abstract class Controller extends BaseController
{
    use DispatchesJobs, JsonPresenter;

    /**
     * store an uploaded file to external storage
     *
     * @param StorageService $storageService
     * @param UploadedFile $uploaded
     * @return array|string                     id of the file on external storage
     */
    protected final function storeUploadFile(StorageService $storageService, UploadedFile $uploaded)
    {
        return $storageService->store(strval($uploaded), [
            'ext' => $uploaded->getExtension(),
        ]);
    }

    /**
     * sane page and size for paginated request
     *
     * @param int $page        page number
     * @param int $size        page size
     * @param array $options   - min_page      the min/starting page number(default to 1)
     *                         - max_size      max item per page
     *                         - default_size  default page size
     *
     * @return array               [0] is saned page#
     *                             [1] is saned page size
     */
    protected final function sanePageAndSize($page, $size, array $options = [])
    {
        if ($page <= 0) {
            $page = array_get($options, 'min_page', 1);
        }

        $maxSize = array_get($options, 'max_size', 100);
        if ($size <=0 || $maxSize <= 0) {
            $size = array_get($options, 'default_size', 15);
        } else if ($size > $maxSize) {
            $size = $maxSize;
        }

        return [$page, $size];
    }

}
