<?php

namespace App\Jobs;

use App\Models\Image;
use Google\Cloud\Vision\V1\AnnotateImageRequest;
use Google\Cloud\Vision\V1\Client\ImageAnnotatorClient; // <-- QUESTO
use Google\Cloud\Vision\V1\Feature;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\Image\Enums\AlignPosition;
use Spatie\Image\Enums\Fit;
use Spatie\Image\Image as SpatieImage;
use Google\Cloud\Vision\V1\Image as VisionImage;
use Google\Cloud\Vision\V1\BatchAnnotateImagesRequest;

class RemoveFaces implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $article_image_id;

    public function __construct($article_image_id)
    {
        $this->article_image_id = $article_image_id;
    }

    public function handle()
    {
        $i = Image::find($this->article_image_id);
        if (!$i) {
            return;
        }

        $srcPath = storage_path('app/public/' . $i->path); // <-- (questa è la forma corretta cross-platform)
        $image = file_get_contents($srcPath);

        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . base_path('google_credential.json'));

        // $imageAnnotator = new ImageAnnotatorClient(); // <-- QUESTO
        // $response = $imageAnnotator->faceDetection($image);
        // $faces = $response->getFaceAnnotations();
       $imageAnnotator = new ImageAnnotatorClient();

        $feature = (new Feature())->setType(Feature\Type::FACE_DETECTION);

        $visionImage = (new VisionImage())->setContent($image);

        $annotateRequest = (new AnnotateImageRequest())
            ->setImage($visionImage)
            ->setFeatures([$feature]);

        $batchRequest = (new BatchAnnotateImagesRequest())
            ->setRequests([$annotateRequest]);

        $batchResponse = $imageAnnotator->batchAnnotateImages($batchRequest);

        $faces = $batchResponse->getResponses()[0]->getFaceAnnotations();

        $imageAnnotator->close();

        foreach ($faces as $face) {
            $vertices = $face->getBoundingPoly()->getVertices();

            $bounds = [];

            foreach ($vertices as $vertex) {
                $bounds[] = [$vertex->getX(), $vertex->getY()];
            }

            $w = $bounds[2][0] - $bounds[0][0];
            $h = $bounds[2][1] - $bounds[0][1];

            $image = SpatieImage::load($srcPath);

            $image->watermark(
                base_path('resources/img/smile.png'),
                AlignPosition::TopLeft,
                paddingX: $bounds[0][0],
                paddingY: $bounds[0][1],
                width: $w,
                height: $h,
                fit: Fit::Stretch
            );

            $image->save($srcPath);
        }

        $imageAnnotator->close();
    }
}