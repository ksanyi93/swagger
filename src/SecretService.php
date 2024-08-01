<?php
namespace SecretServer;

class SecretService {
    private $storage;

    public function __construct() {
        $this->storage = new Storage();
    }

    public function createSecret($data)
    {
        $hash = bin2hex(random_bytes(16));
        $data['hash'] = $hash;
        $data['secretText'] = $this->generateRandomString();
        $this->storage->save($hash, $data);

        return ['hash' => $hash];
    }

    function generateRandomString()
    {
        $fruits = ['apple', 'banana', 'orange', 'grape', 'melon', 'kiwi'];
        $wordsLength = count($fruits);
    
        $randomIndex = rand(0, $wordsLength - 1);
    
        return $fruits[$randomIndex];
    }

    public function getSecret($hash)
    {
        $secret = $this->storage->load($hash);
        if ($secret/*  && $this->isValid($secret) */)
        {
            $this->storage->delete($hash);
            session_unset();
            session_destroy();

            return $secret;
        }

        return null;
    }

    private function isValid($secret)
    {
        $ttl = $secret['ttl'];
        $expiresAt = strtotime($secret['created_at']) + $ttl;
        return time() < $expiresAt;
    }
}
