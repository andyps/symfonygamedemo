<?php


namespace App\Services;

use App\Entity\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class BankApiService
{
    private const DELAY = 3;
    private string $endpoint;
    private Client $client;

    public function __construct(string $bankApiEndpoint)
    {
        $this->endpoint = $bankApiEndpoint . self::DELAY;
        $this->client = new Client([
            'base_uri' => $this->endpoint,
            'timeout'  => 5.0,
        ]);
    }

    /**
     * @param User $user
     * @param int $amount
     * @throws GuzzleException An Http Exception.
     * @throws \Exception If response status code is not 200
     */
    public function sendMoneyToUser(User $user, int $amount)
    {
        $response = $this->client->request('GET', '', [
            'json' => [
                'email' => $user->getEmail(),
                'amount' => $amount
            ]
        ]);


        if (!$this->isResponseSuccessful($response)) {
            throw new \Exception(sprintf(
                'Sending money to user %s failed. Status code: %d',
                $user->getEmail(),
                $response->getStatusCode()
            ));
        }
    }

    private function isResponseSuccessful($response): bool
    {
        return $response->getStatusCode() == 200;
    }
}
