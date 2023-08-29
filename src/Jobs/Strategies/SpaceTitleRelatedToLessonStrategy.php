<?php

namespace EscolaLms\ConsultationAccess\Jobs\Strategies;

class SpaceTitleRelatedToLessonStrategy extends DefaultSpaceTitleStrategy
{
    public function getTitle(): string
    {
        return $this->enquiry->related->course->title . ' ' . parent::getTitle();
    }
}
