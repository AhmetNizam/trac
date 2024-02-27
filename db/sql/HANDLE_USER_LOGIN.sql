BEGIN
  DECLARE user_count INT DEFAULT 0;
  DECLARE result INT DEFAULT 0;

  SELECT COUNT(*) INTO user_count
  FROM USER
  WHERE USERNAME = pUsername;

  IF user_count = 1 THEN
  /*
    SELECT COUNT(*) INTO user_count
    FROM USER
    WHERE USERNAME = pUsername
      AND NAME = pName
      AND SURNAME = pSurname
      AND MAIL = pMail
      AND POSITION = pPosition
      AND DEPARTMENT = pDepartment
      AND LOCATION = pLocation;

    IF user_count = 0 THEN
      UPDATE USER
      SET NAME = pName, SURNAME = pSurname, MAIL = pMail,
          POSITION = pPosition, DEPARTMENT = pDepartment, LOCATION = pLocation
      WHERE USERNAME = pUsername;
      COMMIT;
    END IF;
  */
    SET result = 1;
  ELSE
    INSERT INTO USER
    (USERNAME, NAME, SURNAME, EMAIL, POSITION_ID, DEPARTMENT_ID, LOCATION_ID)
    VALUES
    ('ahmet.nizam1', 'AHMET', 'NİZAM', 'ahmet.nizam1@mlpcare.com', 1, 1, 1);
    COMMIT;

    SET result = 0;
  END IF;

  SELECT ID INTO oUserid
  FROM USER
  WHERE USERNAME = pUsername;
END