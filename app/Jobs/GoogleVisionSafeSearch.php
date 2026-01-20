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

class GoogleVisionSafeSearch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $article_image_id;

    public function __construct($article_image_id)
    {
        $this->article_image_id = $article_image_id;
    }

    public function handle(): void
    {
        $imageModel = Image::find($this->article_image_id);

        if (! $imageModel) {
            return;
        }

        $image = file_get_contents(storage_path('app/public/' . $imageModel->path));
        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . base_path('google_credential.json'));

        $imageAnnotator = new ImageAnnotatorClient();

        $visionImage = (new VisionImage())->setContent($image);

        $feature = (new Feature())->setType(Feature\Type::SAFE_SEARCH_DETECTION);

        $annotateRequest = (new AnnotateImageRequest())
            ->setImage($visionImage)
            ->setFeatures([$feature]);

        $batchRequest = (new BatchAnnotateImagesRequest())
            ->setRequests([$annotateRequest]);

        $batchResponse = $imageAnnotator->batchAnnotateImages($batchRequest);
        $imageAnnotator->close();

        $response = $batchResponse->getResponses()[0];
        $safe = $response->getSafeSearchAnnotation();

        $adult     = $safe->getAdult();
        $medical   = $safe->getMedical();
        $spoof     = $safe->getSpoof();
        $violence  = $safe->getViolence();
        $racy      = $safe->getRacy();

        $likelihoodName = [
            'text-secondary bi bi-circle-fill',
            'text-success bi bi-check-circle-fill',
            'text-success bi bi-check-circle-fill',
            'text-warning bi bi-exclamation-circle-fill',
            'text-warning bi bi-exclamation-circle-fill',
            'text-danger bi bi-dash-circle-fill',
        ];

        $imageModel->adult     = $likelihoodName[$adult];
        $imageModel->spoof     = $likelihoodName[$spoof];
        $imageModel->racy      = $likelihoodName[$racy];
        $imageModel->medical   = $likelihoodName[$medical];
        $imageModel->violence  = $likelihoodName[$violence];

        $imageModel->save();
    }
}
