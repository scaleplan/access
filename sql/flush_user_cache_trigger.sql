CREATE OR REPLACE FUNCTION access.right_update()
RETURNS trigger language plperlu AS
$BODY$
	my $script_path = "/tmp/flush_user_cache.sh";
	my $log_path = "/var/log/update_user_rights.log";

	if ($_TD->{event} == "DELETE") {
		my $user_id = $_TD->{old}{user_id};
		system("$script_path $user_id >> $log_path &");
	} elsif ($_TD->{event} == "INSERT") {
		my $user_id = $_TD->{new}{user_id};
		system("$script_path $user_id >> $log_path &");
	} else {
		my $user_id = $_TD->{old}{user_id};
		system("$script_path $user_id >> $log_path &");
		$user_id = $_TD->{new}{user_id};
		system("$script_path $user_id >> $log_path &");
	}

	return;
$BODY$;

CREATE TRIGGER
	flush_user_cache
AFTER
	INSERT OR UPDATE OR DELETE
ON
	access.access_right
FOR EACH ROW
EXECUTE PROCEDURE
	access.right_update();