

-- TRC 錢包

CREATE TABLE custom_trc_wallet (
    id          INTEGER   NOT NULL PRIMARY KEY,
    member_id   INTEGER   NOT NULL,
    address     TEXT      NOT NULL UNIQUE,
    hex_address TEXT      NOT NULL,
    private_key TEXT      NOT NULL,
    create_time TIMESTAMP NOT NULL,
    status      INTEGER   NOT NULL  -- options: trc-wallet-status
);


