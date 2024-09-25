<?php

namespace Stephenjude\FilamentFeatureFlag\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Stephenjude\FilamentFeatureFlag\Models\FeatureSegment;

class RemovingFeatureSegment
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public FeatureSegment $featureSegment, public mixed $authUser) {}
}
