<?php

namespace Stephenjude\FeaturePlugin\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Stephenjude\FeaturePlugin\Models\FeatureSegment;

class RemovingFeatureSegment
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public FeatureSegment $featureSegment)
    {
    }
}
