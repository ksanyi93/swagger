<?php
namespace SecretServer;

class Storage {
    private $file = __DIR__ . '\fruits.json';

    public function save($hash, $data)
    {
        $currentDate = new \DateTime();
        $data['created_at'] = $currentDate->format('Y-m-d H:i:s');
        $currentDate->modify('+1 day');
        $data['expiresAt'] = $currentDate->format('Y-m-d H:i:s');
        $data['remainingViews'] = rand(1, 3);
        
        $secrets = $this->loadAll();
        $secrets[$hash] = $data;
        file_put_contents($this->file, json_encode($secrets));
    }

    public function load($hash)
    {
        $secrets = $this->loadAll();
        return $secrets[$hash] ?? null;
    }

    public function delete($hash)
    {
        $secrets = $this->loadAll();
        unset($secrets[$hash]);
        file_put_contents($this->file, json_encode($secrets));
    }

    private function loadAll()
    {
        if (file_exists($this->file)) {
            return json_decode(file_get_contents($this->file), true);
        }

        return [];
    }
}
