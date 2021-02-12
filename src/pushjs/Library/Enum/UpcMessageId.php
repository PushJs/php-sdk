<?php

namespace PushJS\Library\Enum;

class UpcMessageId
{
    const MESSAGE_ID_CLIENT_HELLO = 'u65';

    const MESSAGE_ID_CREATE_ROOM = 'u24';

    const MESSAGE_ID_JOIN_ROOM = 'u4';

    const MESSAGE_ID_SEND_MESSAGE_TO_ROOMS = 'u1';

    const SET_CLIENT_ATTR = 'u3';

    const SERVER_HELLO = 'u66';

    const SERVER_CLIENT_INFO = 'u29';

    const MESSAGE_ID_CLIENT_INFO = 'a6';

    const SERVER_CLIENT_METADATA = 'u8';

    const SERVER_READY = 'u63';

    const ATTRIBUTE_DIE_ON_EMPTY = '_DIE_ON_EMPTY';

    const ATTRIBUTE_MAX_CLIENTS = '_MAX_CLIENTS';

    const ATTRIBUTE_PASSWORD = '_PASSWORD';

    const SEND_MODULE_MESSAGE = 'u71';

    const GET_CHANNEL_CLIENTS = 'u55';
}
