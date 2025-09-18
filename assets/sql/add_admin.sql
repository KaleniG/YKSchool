DO $$
BEGIN
    IF EXISTS (
        SELECT FROM information_schema.tables 
        WHERE table_schema = 'public'
          AND table_name = 'administrators'
    ) THEN
        IF NOT EXISTS (
            SELECT 1 FROM administrators 
            WHERE name = 'Yevhenii' 
              AND surname = 'Kaletchuk' 
              AND email = 'eugenio.kaletchuk@gmail.com'
              AND phone_number = '3248304571'
        ) THEN
            INSERT INTO administrators (name, surname, email, phone_number)
            VALUES ('Yevhenii', 'Kaletchuk', 'eugenio.kaletchuk@gmail.com', '3248304571');
            RAISE NOTICE 'Inserted administrator.';
        ELSE
            RAISE NOTICE 'Administrator already exists.';
        END IF;
    ELSE
        RAISE NOTICE 'Table "administrators" does not exist.';
    END IF;
END $$;