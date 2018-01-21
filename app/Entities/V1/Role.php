<?php

namespace App\Entities\V1;

use TCG\Voyager\Models\Role as TCGRole;


class Role extends TCGRole
{

    protected $fillable = ['name', 'display_name'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function toArray() {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'display_name' => $this->display_name
        ];
    }

}
