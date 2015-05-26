<?php

namespace Service\WithBad\ClassFiles{

    use \DateTime;

    final class ServiceSettings
    {
        const ServiceNamespace = 'https://some.endpoint.com/Api/v1';
        const ProductionEndpoint = 'https://some.endpoint.com/Api/v1/UnIntelligent.svc';
    }

    /** comment */
    final class TimeInterval
    {
        /** Use data from the previous calendar month. */
        const Last30Days = 'Last30Days';

        /** Use data from last week, Sunday through Saturday. */
        const Last7Days = 'Last7Days';

        /** Use data from yesterday. */
        const LastDay = 'LastDay';
    }

    /**
     * Defines something.
     *
     * @link http://some.documentation.com TimeInterval Value Set
     */
    final class Scale
    {
        const Minimal = 'Minimal';
        const Low = 'Low';
        const Medium = 'Medium';
        const High = 'High';
        const VeryHigh = 'VeryHigh';
    }
}