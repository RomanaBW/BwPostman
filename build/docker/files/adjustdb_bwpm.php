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

    // Add users
    $query = getQueryToInsertUsers();
//    echo "\n\ngetQueryToInsertUsers: \n" . $query . "\n";

    if (!$mysql->query($query))
    {
        fwrite($stderr, "\nMySQL 'Add users' Error: " . $mysql->error . "\n");
        $mysql->close();
        exit(1);
    }

    // Add users to user groups
    $query = getQueryToInsertUsersToGroups();
//    echo "\n\ngetQueryToInsertUsersToGroups: \n" . $query . "\n";

    if (!$mysql->query($query))
    {
        fwrite($stderr, "\nMySQL 'Add users to groups' Error: " . $mysql->error . "\n");
        $mysql->close();
        exit(1);
    }

    // Add token for cron user
    $query = getQueryToCreateToken();
//    echo "\n\ngetQueryToCreateToken: \n" . $query . "\n";

    if (!$mysql->query($query))
    {
        fwrite($stderr, "\nMySQL 'Add token for cron user' Error: " . $mysql->error . "\n");
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

function getQueryToInsertUsers(): string
{
    $query = 'REPLACE INTO `jos_users`(
        `id`,
        `name`,
        `username`,
        `email`,
        `password`,
        `block`,
        `sendEmail`,
        `registerDate`,
        `activation`,
        `params`,
        `resetCount`,
        `requireReset`
        )
    VALUES
         (
            \'757\',
            \'AdminTester\',
            \'AdminTester\',
            \'info@boldt-services.de\',
            \'$2y$10$VnyP/VSrn9ULXuD6lzEsFeud1uR78wq6jSRCgVaiIkc84Gyj589nO\',
            \'0\',
            \'0\',
            \'2019-04-02 13:18:00\',
            \'\',
            \'\',
            \'0\',
            \'0\'
        ),
       (
            \'847\',
            \'Cronuser\',
            \'Cronuser\',
            \'bwpostman@boldt-webservice.de\',
            \'$2y$10$2.aWsrnYNc.Fstb.qF83deLquBhwEoFMIayRSSfUy3.qziFQLvzTq\',
            \'0\',
            \'0\',
            \'2019-04-02 13:18:00\',
            \'\',
            \'\',
            \'0\',
            \'0\'
        ),
        (
            \'827\',
            \'BwPostmanAdmin\',
            \'BwPostmanAdmin\',
            \'BwPostmanAdmin@tester-net.nil\',
            \'$2y$10$mxfpwA8/qqKUD/XJjgHL2.CjEB2qoKqD.Q9Q1E4ovdQvv73v28wlq\',
            \'0\',
            \'0\',
            \'2019-04-02 13:18:00\',
            \'\',
            \'\',
            \'0\',
            \'0\'
        ),
        (
            \'828\',
            \'BwPostmanPublisher\',
            \'BwPostmanPublisher\',
            \'BwPostmanPublisher@tester-net.nil\',
            \'$2y$10$kpZTpYDOapWluC5.WXVvCOJGwSiomolS6F2Wl1kGWy18EFR2sTOVm\',
            \'0\',
            \'0\',
            \'2019-04-02 13:18:00\',
            \'\',
            \'\',
            \'0\',
            \'0\'
        ),
        (
            \'829\',
            \'BwPostmanEditor\',
            \'BwPostmanEditor\',
            \'BwPostmanEditor@tester-net.nil\',
            \'$2y$10$7X3pOsWZWblHT200o/hj2OFUJIsCaHvuzPt4kRrdirGM6DOaM0Via\',
            \'0\',
            \'0\',
            \'2019-04-02 13:18:00\',
            \'\',
            \'\',
            \'0\',
            \'0\'
        ),
        (
            \'830\',
            \'BwPostmanCampaignAdmin\',
            \'BwPostmanCampaignAdmin\',
            \'BwPostmanCampaignAdmin@tester-net.nil\',
            \'$2y$10$HSVZrT2Fg1IbLLQ7aQgFuOacwPNeUS9IeYePAw90fU7xM3DeFu.O2\',
            \'0\',
            \'0\',
            \'2019-04-02 13:18:00\',
            \'\',
            \'\',
            \'0\',
            \'0\'
        ),
        (
            \'831\',
            \'BwPostmanCampaignPublisher\',
            \'BwPostmanCampaignPublisher\',
            \'BwPostmanCampaignPublisher@tester-net.nil\',
            \'$2y$10$vJVdNHo/2B4f/ESYZ1uChe4XB3gYQbf06eeirjYHbDuHYIW8bEOSG\',
            \'0\',
            \'0\',
            \'2019-04-02 13:18:00\',
            \'\',
            \'\',
            \'0\',
            \'0\'
        ),
        (
            \'832\',
            \'BwPostmanCampaignEditor\',
            \'BwPostmanCampaignEditor\',
            \'BwPostmanCampaignEditor@tester-net.nil\',
            \'$2y$10$7TZC8LquSVCFQlMAEjRp9u1LD7rkot3QwjUIuZeIHG0QHcKzIxO9m\',
            \'0\',
            \'0\',
            \'2019-04-02 13:18:00\',
            \'\',
            \'\',
            \'0\',
            \'0\'
        ),
        (
            \'833\',
            \'BwPostmanMailinglistAdmin\',
            \'BwPostmanMailinglistAdmin\',
            \'BwPostmanMailinglistAdmin@tester-net.nil\',
            \'$2y$10$M7RmA61wzjifPLIlH9IyLe.6Bb1HXOWFFetFMV1CSIybe6plXScdu\',
            \'0\',
            \'0\',
            \'2019-04-02 13:18:00\',
            \'\',
            \'\',
            \'0\',
            \'0\'
        ),
        (
            \'834\',
            \'BwPostmanMailinglistPublisher\',
            \'BwPostmanMailinglistPublisher\',
            \'BwPostmanMailinglistPublisher@tester-net.nil\',
            \'$2y$10$Xba8L4rw8rmF0zYssTWZme0g4oDDub1Lm5q5yXl0q1VZxt3z7kfwG\',
            \'0\',
            \'0\',
            \'2019-04-02 13:18:00\',
            \'\',
            \'\',
            \'0\',
            \'0\'
        ),
        (
            \'835\',
            \'BwPostmanMailinglistEditor\',
            \'BwPostmanMailinglistEditor\',
            \'BwPostmanMailinglistEditor@tester-net.nil\',
            \'$2y$10$jh93GuujWvAkA3sABNGONeFZDyP5rehdno75egFAx0wLsASymVtQC\',
            \'0\',
            \'0\',
            \'2019-04-02 13:18:00\',
            \'\',
            \'\',
            \'0\',
            \'0\'
        ),
        (
            \'836\',
            \'BwPostmanManager\',
            \'BwPostmanManager\',
            \'BwPostmanManager@tester-net.nil\',
            \'$2y$10$l1J9RXOUBGgTGrOo1Xzw.uTu2IynGIhRUyNmbklo53m79AMJKdNvS\',
            \'0\',
            \'0\',
            \'2019-04-02 13:18:00\',
            \'\',
            \'\',
            \'0\',
            \'0\'
        ),
        (
            \'837\',
            \'BwPostmanNewsletterAdmin\',
            \'BwPostmanNewsletterAdmin\',
            \'BwPostmanNewsletterAdmin@tester-net.nil\',
            \'$2y$10$Prz8O.7/qwLYBezAk9patOagpcKvdIr1823SX3Kfb.tukY/zTFcQy\',
            \'0\',
            \'0\',
            \'2019-04-02 13:18:00\',
            \'\',
            \'\',
            \'0\',
            \'0\'
        ),
        (
            \'838\',
            \'BwPostmanNewsletterPublisher\',
            \'BwPostmanNewsletterPublisher\',
            \'BwPostmanNewsletterPublisher@tester-net.nil\',
            \'$2y$10$dwjLgbGiExBRJVuYJGkyWug6TqBZlQLeJ0xidbgtDLszT34dNXtvC\',
            \'0\',
            \'0\',
            \'2019-04-02 13:18:00\',
            \'\',
            \'\',
            \'0\',
            \'0\'
        ),
        (
            \'839\',
            \'BwPostmanNewsletterEditor\',
            \'BwPostmanNewsletterEditor\',
            \'BwPostmanNewsletterEditor@tester-net.nil\',
            \'$2y$10$HrZqC3AsPdsnashaa8SlL.esKxiOruuN3mXaBw.KsckBnpWZObrmG\',
            \'0\',
            \'0\',
            \'2019-04-02 13:18:00\',
            \'\',
            \'\',
            \'0\',
            \'0\'
        ),
        (
            \'841\',
            \'BwPostmanSubscriberAdmin\',
            \'BwPostmanSubscriberAdmin\',
            \'BwPostmanSubscriberAdmin@tester-net.nil\',
            \'$2y$10$w7tTeHu/DpD9mRj8EvBPR.woymoCad8Bl6WXHgZVXf3fZV6jEw/vi\',
            \'0\',
            \'0\',
            \'2019-04-02 13:18:00\',
            \'\',
            \'\',
            \'0\',
            \'0\'
        ),
        (
            \'842\',
            \'BwPostmanSubscriberPublisher\',
            \'BwPostmanSubscriberPublisher\',
            \'BwPostmanSubscriberPublisher@tester-net.nil\',
            \'$2y$10$n6fer.DvX8UdZghaZL9eZuPFlT04isSSZECSBzkphhi1bMXNuRCCC\',
            \'0\',
            \'0\',
            \'2019-04-02 13:18:00\',
            \'\',
            \'\',
            \'0\',
            \'0\'
        ),
        (
            \'843\',
            \'BwPostmanSubscriberEditor\',
            \'BwPostmanSubscriberEditor\',
            \'BwPostmanSubscriberEditor@tester-net.nil\',
            \'$2y$10$Fxly5yTHuFFhO4RDhzmfYOyRUoLYDMQBXkTnMtgkhrcYBb4f8xevy\',
            \'0\',
            \'0\',
            \'2019-04-02 13:18:00\',
            \'\',
            \'\',
            \'0\',
            \'0\'
        ),
        (
            \'844\',
            \'BwPostmanTemplateAdmin\',
            \'BwPostmanTemplateAdmin\',
            \'BwPostmanTemplateAdmin@tester-net.nil\',
            \'$2y$10$xURiJFQo.Ux9qttq5PkN.eUDpmlF8CTtlfaFXWHywDQZJEQa3NHtG\',
            \'0\',
            \'0\',
            \'2019-04-02 13:18:00\',
            \'\',
            \'\',
            \'0\',
            \'0\'
        ),
        (
            \'845\',
            \'BwPostmanTemplateEditor\',
            \'BwPostmanTemplateEditor\',
            \'BwPostmanTemplateEditor@tester-net.nil\',
            \'$2y$10$1h2.bkrKLa3y2DivXZsLLO82RQuaUKZ5s5j1lP1RDsemNHxFYsyM6\',
            \'0\',
            \'0\',
            \'2019-04-02 13:18:00\',
            \'\',
            \'\',
            \'0\',
            \'0\'
        ),
        (
            \'846\',
            \'BwPostmanTemplatePublisher\',
            \'BwPostmanTemplatePublisher\',
            \'BwPostmanTemplatePublisher@tester-net.nil\',
            \'$2y$10$TGzoo.9Bxw0Uj9N6ZIkd6OCj3ykA3fGxY0Xvg.IJw7us1PAuk23w2\',
            \'0\',
            \'0\',
            \'2019-04-02 13:18:00\',
            \'\',
            \'\',
            \'0\',
            \'0\'
        )
        ';

    return $query;
}

function getQueryToInsertUsersToGroups(): string
{
    $query = 'REPLACE INTO `jos_user_usergroup_map` VALUES 
        (\'847\',\'8\')';

    return $query;
}

function getQueryToCreateToken(): string
{
    $query = 'REPLACE INTO `jos_user_profiles` VALUES 
        (\'847\',\'joomlatoken.enabled\',\'1\',\'2\'),
        (\'847\',\'joomlatoken.token\',\'iqvWu81yktBv8PffwuqboxxKAz0DUcmtEGI45vzdxuM=\',\'1\')'
    ;

    return $query;
}
