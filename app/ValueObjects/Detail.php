<?php

namespace App\ValueObjects;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

class Detail implements Arrayable, JsonSerializable
{
    public string $type;
    public float|int $amount;
    public string $description;

    /**
     * Create a new Detail instance.
     */
    public function __construct(array $data)
    {
        $this->type = $data['type'];
        $this->amount = $data['amount'];
        $this->description = $data['description'];
    }

    /**
     * Get the instance as an array.
     *
     * @return array{type: string, amount: float|int, description: string}
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'amount' => $this->amount,
            'description' => $this->description,
        ];
    }

    /**
     * Specify the data which should be serialized to JSON.
     *
     * @return array{type: string, amount: float|int, description: string}
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}