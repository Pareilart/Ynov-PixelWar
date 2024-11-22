<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});


Broadcast::channel('post-pixel-create', function () {
    return true;
});

Broadcast::channel('post-pixel-delete', function () {
    return true;
});
