## accepted_requests ## 
	IN uId INT

<pre>BEGIN
	SELECT u.Username, r.* FROM requests r JOIN users u ON r.requestor = u.id WHERE creator = uId;
END$$</pre>


## all_products ## 

<pre>BEGIN
	SELECT u.Username, p.* FROM products p JOIN users u ON p.Creator = u.id;
END$$</pre>


## all_requests ## 

<pre>BEGIN
	SELECT u.Username, r.* FROM requests r JOIN users u ON r.requestor = u.id WHERE r.creator IS NULL ORDER BY r.created DESC;
END$$</pre>


## can_access_request ## 
	IN rId INT,
    IN uId INT

<pre>BEGIN
	SELECT COALESCE(
		(SELECT 1 FROM requests r JOIN users u ON u.id = r.requestor WHERE r.id = rId), /* Case 1, select if the user owns the request */
        (SELECT 1 FROM users WHERE id = uId AND (isAdmin = 1 OR isCreator = 1)), /* Case 2, user is creator or admin */
        0
    ) AS 'access' FROM DUAL;	
END$$</pre>


## can_update ## 
	IN pId INT,
    IN uId INT

<pre>BEGIN
	SELECT Image, count(*) AS `valid` FROM products WHERE Creator = uId AND id = pId;
END$$</pre>


## claim_request ## 
	IN rId INT,
    IN uId INT

<pre>BEGIN
	UPDATE requests SET creator=uId WHERE id = rId;
END$$</pre>


## delete_product ## 
	IN pId INT

<pre>BEGIN
	DELETE FROM products WHERE id = pId;
END$$</pre>


## delete_request ## 
	IN rId INT

<pre>BEGIN
	DELETE FROM requests WHERE id = rId;
END$$</pre>


## get_product ## 
	IN pId INT

<pre>BEGIN
	SELECT u.Username, p.* FROM products p JOIN users u ON p.Creator = u.id WHERE p.id = pId;
END$$</pre>


## get_products ## 
	IN cId INT

<pre>BEGIN
	SELECT u.Username, p.* FROM products p JOIN users u ON p.Creator = u.id WHERE Creator = cId;
END$$</pre>


## get_request ## 
	IN rId INT

<pre>BEGIN
	SELECT u.Username, r.* FROM requests r JOIN users u ON r.requestor = u.id WHERE r.id = rId;
END$$</pre>


## get_user ## 
	IN user_id INT

<pre>BEGIN
	SELECT * FROM users WHERE id = user_id;
END$$</pre>


## new_product ## 
	IN pName VARCHAR(100),
    IN pUser int, 
    IN pDescription VARCHAR(250),
    IN pImage VARCHAR(100),
    IN pPrice DECIMAL(6,2)

<pre>BEGIN
	INSERT INTO products
(`Name`,
`Creator`,
`Description`,
`Image`,
`Price`)
VALUES
(pName,
pUser,
pDescription,
pImage,
pPrice);

    SELECT LAST_INSERT_ID() AS 'id';
END$$</pre>


## new_request ## 
	IN rUser int, 
	IN rName VARCHAR(100),
    IN rDescription VARCHAR(250),
    IN rImage VARCHAR(100)

<pre>BEGIN
	INSERT INTO requests
(`requestor`,
`name`,
`description`,
`image`,
`created`)
VALUES
(rUser,
rName,
rDescription,
rImage,
NOW());

    SELECT LAST_INSERT_ID() AS 'id';
END$$</pre>


## new_user ## 
    IN Email VARCHAR(100),
    IN `password` VARCHAR(100), 
    IN Username VARCHAR(45)

<pre>BEGIN
	insert into users(`Registered`, `Email`, `password`, `Username`) values(CURDATE(), Email, `password`, Username);
    SELECT LAST_INSERT_ID() AS 'id';
END$$</pre>


## update_product ## 
	IN pName VARCHAR(100),
    IN pDescription VARCHAR(250),
    IN pImage VARCHAR(100),
    IN pPrice DECIMAL(6,2),
    IN pId INT

<pre>BEGIN
	CASE
		WHEN pImage = '' THEN
			UPDATE products SET
				`Name` = pName,
				`Description` = pDescription,
				`Price` = pPrice
			WHERE id = pId;
		ELSE
			UPDATE products SET
				`Name` = pName,
				`Description` = pDescription,
				`Image` = pImage,
				`Price` = pPrice
			WHERE id = pId;
	END CASE;
END$$</pre>


## user_exists ## 
	IN em VARCHAR(100),
    IN uname VARCHAR(45)

<pre>BEGIN
	SELECT 
		(SELECT count(*) FROM users WHERE Email = em) AS 'Email',
        (SELECT count(*) FROM users WHERE Username = uname) AS 'Username'
	FROM DUAL;
END$$</pre>


## user_hash ## 
	IN uname VARCHAR(45)

<pre>BEGIN
	SELECT id, `password` FROM users WHERE Username = uname;
END$$</pre>


## user_requests ## 
	IN uId INT

<pre>BEGIN
	SELECT * FROM requests WHERE requestor = uId;
END$$</pre>

