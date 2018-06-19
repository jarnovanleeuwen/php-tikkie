<?php
namespace PHPTikkie\Requests;

use DateTime;
use DateTimeInterface;
use DateTimeZone;

class FetchPaymentRequestsRequest extends AbstractRequest
{
    /**
     * @var string
     */
    protected $platformToken;

    /**
     * @var string
     */
    protected $userToken;

    public function __construct(string $platformToken, string $userToken, int $offset, int $limit, DateTimeInterface $fromDate = null, DateTimeInterface $toDate = null)
    {
        $this->platformToken = $platformToken;
        $this->userToken = $userToken;

        $params = compact('offset', 'limit');

        if ($fromDate) {
            $params['fromDate'] = $this->formatDateTime($fromDate);
        }

        if ($toDate) {
            $params['toDate'] = $this->formatDateTime($toDate);
        }

        $this->parameters = $params;
    }

    public function getMethod(): string
    {
        return 'GET';
    }

    public function getUri(): string
    {
        return "v1/tikkie/platforms/{$this->platformToken}/users/{$this->userToken}/paymentrequests";
    }

    /**
     * Use UTC time zone and return ISO-8601 format.
     */
    protected function formatDateTime(DateTimeInterface $date): string
    {
        return (new DateTime)
            ->setTimestamp($date->getTimestamp())
            ->setTimezone(new DateTimeZone('UTC'))
            ->format('Y-m-d\TH:i:s\Z');
    }
}
