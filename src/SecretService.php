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
        $data['secretText'] = $this->pickOneRandomString();
        $data['remainingViews'] = rand(1, 3);
        $this->storage->save($hash, $data);

        return ['hash' => $hash, 'remainingViews' => $data['remainingViews']];
    }

    function pickOneRandomString()
    {
        $fruits = ['apple', 'banana', 'orange', 'grape', 'melon', 'kiwi'];
        $wordsLength = count($fruits);
    
        $randomIndex = rand(0, $wordsLength - 1);
    
        return $fruits[$randomIndex];
    }

    public function getSecret($hash, $remainingViews)
    {
        $secret = $this->storage->load($hash);
        if ($secret) {
            $this->storage->delete($secret);
            return $secret;
        } elseif ($this->isValid($secret)) {
            return null;
        }

        return null;
    }

    private function isValid($secret)
    {
        date_default_timezone_set('Europe/Budapest');

        $expiresAt = isset($secret['expiresAt']) ? strtotime($secret['expiresAt']) : false;

        return (time() > $expiresAt) ? false : true;
    }
}
