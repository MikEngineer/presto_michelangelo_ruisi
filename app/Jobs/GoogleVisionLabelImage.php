<?php

namespace App\Jobs;

use App\Models\Image;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Google\Cloud\Vision\V1\Client\ImageAnnotatorClient;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Google\Cloud\Vision\V1\AnnotateImageRequest;
use Google\Cloud\Vision\V1\BatchAnnotateImagesRequest;
use Google\Cloud\Vision\V1\Feature;
use Google\Cloud\Vision\V1\Image as VisionImage;

class GoogleVisionLabelImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $article_image_id;

    public function __construct($article_image_id)
    {
        $this->article_image_id = $article_image_id;
    }

    public function handle(): void
    {
        $i = Image::find($this->article_image_id);

        if (! $i) {
            return;
        }

        $image = file_get_contents(storage_path('app/public/' . $i->path));
        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . base_path('google_credential.json'));

        $imageAnnotator = new ImageAnnotatorClient();

        $visionImage = (new VisionImage())->setContent($image);

        $feature = (new Feature())->setType(Feature\Type::LABEL_DETECTION);

        $annotateRequest = (new AnnotateImageRequest())
            ->setImage($visionImage)
            ->setFeatures([$feature]);

        $batchRequest = (new BatchAnnotateImagesRequest())
            ->setRequests([$annotateRequest]);

        $batchResponse = $imageAnnotator->batchAnnotateImages($batchRequest);

        $response = $batchResponse->getResponses()[0];
        $labels = $response->getLabelAnnotations();

        if ($labels) {
            $result = [];

            foreach ($labels as $label) {
                $result[] = $label->getDescription();
            }

            $i->labels = $result;
            $i->save();
        }

        $imageAnnotator->close();
    }
}
