<?php

namespace App\Application\Whois\Services;

use App\Application\Whois\DTO\WhoisLookupRequestDto;
use App\Application\Whois\DTO\WhoisLookupResponseDto;
use Iodev\Whois\Exceptions\ConnectionException;
use Iodev\Whois\Exceptions\ServerMismatchException;
use Iodev\Whois\Exceptions\WhoisException;
use Iodev\Whois\Factory;

final class WhoisLookupService
{
    public function lookup(WhoisLookupRequestDto $request): WhoisLookupResponseDto
    {
        try {
            $whois = Factory::get()->createWhois();

            if ($whois->isDomainAvailable($request->domain)) {
                return new WhoisLookupResponseDto(
                    status: 1,
                    domain: $request->domain,
                    available: true,
                );
            }

            $domainInfo = $whois->loadDomainInfo($request->domain);
            $rawData = $domainInfo?->getData() ?? [];

            return new WhoisLookupResponseDto(
                status: 1,
                domain: $request->domain,
                available: false,
                info: $this->normalizeInfo($rawData),
            );
        } catch (ConnectionException) {
            return new WhoisLookupResponseDto(
                status: 0,
                domain: $request->domain,
                message: 'Disconnect or connection timeout',
            );
        } catch (ServerMismatchException) {
            return new WhoisLookupResponseDto(
                status: 0,
                domain: $request->domain,
                message: 'TLD server not found in current server hosts',
            );
        } catch (WhoisException $exception) {
            return new WhoisLookupResponseDto(
                status: 0,
                domain: $request->domain,
                message: "Whois server responded with error '{$exception->getMessage()}'",
            );
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalizeInfo(array $data): array
    {
        $result = [];

        foreach ($data as $key => $value) {
            if (empty($value)) {
                continue;
            }

            if (str_contains((string) $key, 'Date') && is_numeric($value)) {
                $result[$key] = date('Y-m-d', (int) $value);

                continue;
            }

            if (is_array($value)) {
                foreach ($value as $nestedKey => $nestedValue) {
                    if (empty($nestedValue)) {
                        continue;
                    }

                    $result[$key.$nestedKey] = $nestedValue;
                }

                continue;
            }

            $result[$key === 'domainName' ? 'domain' : $key] = $value;
        }

        return $result;
    }
}
