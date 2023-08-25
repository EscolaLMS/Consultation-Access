<?php

namespace EscolaLms\ConsultationAccess\Jobs\Strategies;

class SpaceTitleRelatedToCourseStrategy extends DefaultSpaceTitleStrategy
{
    public function getTitle(): string
    {
        return $this->enquiry->related->title . ' ' . parent::getTitle();
    }
}
