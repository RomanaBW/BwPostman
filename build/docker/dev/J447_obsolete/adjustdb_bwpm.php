<?php
// Args: 0 => adjustdb.php, 1 => "$JOOMLA_DB_HOST", 2 => "$JOOMLA_DB_USER", 3 => "$JOOMLA_DB_PASSWORD", 4 => "$JOOMLA_DB_NAME", 5 => "$JOOMLA_DB_TYPE"
$stderr = fopen('php://stderr', 'w');
fwrite($stderr, "\nEnsuring Joomla database is present\n");

if (strpos($argv[1], ':') !== false)
{
    list($host, $port) = explode(':', $argv[1], 2);
}
else
{
    $host = $argv[1];
    $port = null;
}

$user = $argv[2];
$password = $argv[3];
$db = $argv[4];
$dbType = strtolower($argv[5]);

if ($dbType === 'mysqli')
{
    $port = $port ? (int)$port : 3306;
    $maxTries = 10;

    // set original default behaviour for PHP 8.1 and higher
    // see https://www.php.net/manual/en/mysqli-driver.report-mode.php
    mysqli_report(MYSQLI_REPORT_OFF);
    do {
        $mysql = new mysqli($host, $user, $password, $db, $port);

        if ($mysql->connect_error)
        {
            fwrite($stderr, "\nMySQL Connection Error: ({$mysql->connect_errno}) {$mysql->connect_error}\n");
            --$maxTries;

            if ($maxTries <= 0)
            {
                exit(1);
            }

            sleep(3);
        }
    } while ($mysql->connect_error);

    // Create bwpostman user groups
    $query = getQueryToInsertBwUserGroups();
//    echo "\n\getQueryToInsertBwUserGroups: \n" . $query . "\n";

    if (!$mysql->query($query))
    {
        fwrite($stderr, "\nMySQL 'Add bwpostman user groups' Error: " . $mysql->error . "\n");
        $mysql->close();
        exit(1);
    }

    // Map bwpostman users to user groups
    $query = getQueryToInsertUsersToGroups();
//    echo "\n\ngetQueryToInsertUsersToGroups: \n" . $query . "\n";

    if (!$mysql->query($query))
    {
        fwrite($stderr, "\nMySQL 'Add users to groups' Error: " . $mysql->error . "\n");
        $mysql->close();
        exit(1);
    }

    fwrite($stderr, "\nMySQL Database Adjusted\n");

    $mysql->close();
}
elseif ($dbType === 'pgsql')
{
    $port = $port ? (int)$port : 5432;
    $maxTries = 10;

    do {
        $connection = "host={$host} port={$port} user={$user} password={$password}";
        $dbconn = @pg_connect($connection);

        if (!$dbconn)
        {
            fwrite($stderr, "\nPostgreSQL Connection Error\n");
            --$maxTries;

            if ($maxTries <= 0)
            {
                exit(1);
            }

            sleep(3);
        }
    } while (!$dbconn);

    $query = "SELECT 1 FROM pg_database WHERE datname = '$db'";
    $result = pg_query($dbconn, $query);

    if (pg_num_rows($result) == 0)
    {
        $createDbQuery = "CREATE DATABASE \"$db\"";
        if (!pg_query($dbconn, $createDbQuery))
        {
            fwrite($stderr, "\nPostgreSQL 'CREATE DATABASE' Error\n");
            pg_close($dbconn);
            exit(1);
        }
    }

    fwrite($stderr, "\nPostgreSQL Database Created\n");

    pg_close($dbconn);
}
else
{
    fwrite($stderr, "\nInvalid database type. Please provide 'pgsql' or 'mysqli'.\n");
    exit(1);
}

function getQueryToInsertBwUserGroups(): string
{
    $query = 'REPLACE INTO `jos_usergroups` (`id`, `parent_id`, `lft`, `rgt`, `title`) VALUES
        (\'10\',\'1\',\'2\',\'39\',\'BwPostmanAdmin\'),
        (\'11\',\'10\',\'3\',\'38\',\'BwPostmanManager\'),
        (\'12\',\'11\',\'22\',\'25\',\'BwPostmanPublisher\'),
        (\'13\',\'12\',\'23\',\'24\',\'BwPostmanEditor\'),
        (\'14\',\'11\',\'10\',\'15\',\'BwPostmanMailinglistAdmin\'),
        (\'15\',\'14\',\'11\',\'14\',\'BwPostmanMailinglistPublisher\'),
        (\'16\',\'15\',\'12\',\'13\',\'BwPostmanMailinglistEditor\'),
        (\'17\',\'11\',\'26\',\'31\',\'BwPostmanSubscriberAdmin\'),
        (\'18\',\'17\',\'27\',\'30\',\'BwPostmanSubscriberPublisher\'),
        (\'19\',\'18\',\'28\',\'29\',\'BwPostmanSubscriberEditor\'),
        (\'20\',\'11\',\'16\',\'21\',\'BwPostmanNewsletterAdmin\'),
        (\'21\',\'20\',\'17\',\'20\',\'BwPostmanNewsletterPublisher\'),
        (\'22\',\'21\',\'18\',\'19\',\'BwPostmanNewsletterEditor\'),
        (\'23\',\'11\',\'4\',\'9\',\'BwPostmanCampaignAdmin\'),
        (\'24\',\'23\',\'5\',\'8\',\'BwPostmanCampaignPublisher\'),
        (\'25\',\'24\',\'6\',\'7\',\'BwPostmanCampaignEditor\'),
        (\'26\',\'11\',\'32\',\'37\',\'BwPostmanTemplateAdmin\'),
        (\'27\',\'26\',\'33\',\'36\',\'BwPostmanTemplatePublisher\'),
        (\'28\',\'27\',\'34\',\'35\',\'BwPostmanTemplateEditor\')';

    return $query;
}

function getQueryToInsertUsersToGroups(): string
{
    $query = 'REPLACE INTO `jos_user_usergroup_map` VALUES 
        (\'827\',\'10\'),
        (\'828\',\'12\'),
        (\'829\',\'13\'),
        (\'830\',\'23\'),
        (\'831\',\'24\'),
        (\'832\',\'25\'),
        (\'833\',\'14\'),
        (\'834\',\'15\'),
        (\'835\',\'16\'),
        (\'836\',\'11\'),
        (\'837\',\'20\'),
        (\'838\',\'21\'),
        (\'839\',\'22\'),
        (\'841\',\'17\'),
        (\'842\',\'18\'),
        (\'843\',\'19\'),
        (\'844\',\'26\'),
        (\'845\',\'28\'),
        (\'846\',\'27\'),
        (\'847\',\'8\')';

    return $query;
}
