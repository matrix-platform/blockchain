

-- 商戶

CREATE TABLE custom_merchant (
    id                  INTEGER          NOT NULL PRIMARY KEY,
    merchant_no         TEXT             NOT NULL UNIQUE,
    title               TEXT             NOT NULL,
    api_url             TEXT             NOT NULL,
    api_secret          TEXT             NOT NULL,
    trc_usdt_wallet     TEXT             NOT NULL, -- TRC-USDT錢包
    trc_usdt_threshold  DOUBLE PRECISION NOT NULL,
    trx_wallet          TEXT             NOT NULL, -- TRX錢包
    trx_private_key     TEXT             NOT NULL, -- TRX錢包密鑰
    trx_safety_balance  INTEGER          NOT NULL, -- TRX錢包安全存量
    trx_recharge_amount INTEGER          NOT NULL, -- Tron錢包TRX補充量/次
    trx_safety_amount   INTEGER          NOT NULL, -- Tron錢包TRX安全存量
    begin_date          DATE                 NULL,
    expire_date         DATE                 NULL,
    disabled            BOOLEAN          NOT NULL
);


-- Tron 錢包

CREATE TABLE custom_tron_wallet (
    id          INTEGER   NOT NULL PRIMARY KEY,
    merchant_id INTEGER   NOT NULL,
    username    TEXT          NULL,
    address     TEXT      NOT NULL UNIQUE,
    hex_address TEXT      NOT NULL,
    private_key TEXT      NOT NULL,
    create_time TIMESTAMP NOT NULL,
    status      INTEGER   NOT NULL  -- options: tron-wallet-status
);


-- Tron 交易

CREATE TABLE custom_tron_transaction (
    id            INTEGER          NOT NULL PRIMARY KEY,
    hash          TEXT             NOT NULL UNIQUE,
    sender        TEXT             NOT NULL,
    receiver      TEXT             NOT NULL,
    type          INTEGER          NOT NULL, -- options: tron-tx-type
    amount        DOUBLE PRECISION NOT NULL,
    fee           DOUBLE PRECISION NOT NULL,
    collection_id INTEGER              NULL,
    notify_time   TIMESTAMP            NULL,
    confirm_time  TIMESTAMP            NULL,
    create_time   TIMESTAMP        NOT NULL,
    status        INTEGER          NOT NULL  -- options: tron-tx-status
);


-- Tron 異常

CREATE TABLE custom_tron_exception (
    id          INTEGER          NOT NULL PRIMARY KEY,
    hash        TEXT             NOT NULL,
    sender      TEXT             NOT NULL,
    receiver    TEXT             NOT NULL,
    amount      DOUBLE PRECISION NOT NULL,
    notify_time TIMESTAMP            NULL,
    create_time TIMESTAMP        NOT NULL
);


