<?php

namespace Tests\Unit;

use App\Enums\HttpStatusCodeEnum;
use App\Models\Associate;
use Illuminate\Support\Collection;
use Tests\TestCase;

class AssociateTest extends TestCase
{
    public function testCanCreateAssociate()
    {
        $data = [
            'name' => $this->faker->name,
            'document' => $this->faker->cpf,
        ];

        $this->post(route('associates.store'), $data)
            ->assertStatus(HttpStatusCodeEnum::CREATED)
            ->assertJson($data);
    }

    public function testCanUpdateAssociate()
    {
        /** @var Associate $schedule */
        $associate = factory(Associate::class)->create();

        $data = [
            'name' => $this->faker->name,
            'document' => $this->faker->cpf,
        ];

        $this->put(route('associates.update', $associate->id), $data)
            ->assertStatus(HttpStatusCodeEnum::SUCCESS)
            ->assertJson($data);
    }

    public function testCanShowAssociate()
    {
        /** @var Associate $schedule */
        $associate = factory(Associate::class)->create();

        $this->get(route('associates.show', $associate->id))
            ->assertStatus(HttpStatusCodeEnum::SUCCESS);
    }

    public function testCanDeleteAssociate()
    {
        /** @var Associate $schedule */
        $associate = factory(Associate::class)->create();

        $this->delete(route('associates.destroy', $associate->id))
            ->assertStatus(HttpStatusCodeEnum::NO_CONTENT);
    }

    public function testCanListAssociates()
    {
        /** @var Collection $associates */
        $associates = factory(Associate::class, 2)->create()->map(function (Associate $associate) {
            return $associate->only([ 'id', 'name', 'document' ]);
        });

        $this->get(route('associates.index'))
            ->assertStatus(HttpStatusCodeEnum::SUCCESS)
            ->assertJson($associates->toArray())
            ->assertJsonStructure([
                '*' =>[ 'id', 'name', 'document' ]
            ]);
    }

    public function testCanNotCreateAssociateWithExistentDocument()
    {
        $data = [
            'name' => $this->faker->name,
            'document' => $this->faker->cpf,
        ];

        $this->post(route('associates.store'), $data);

        $response = [
            'message' => trans('exceptions.System Existing Document'),
        ];

        $this->post(route('associates.store'), $data)
            ->assertStatus(HttpStatusCodeEnum::FORBIDDEN)
            ->assertJson($response);
    }
}
