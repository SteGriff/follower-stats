# Process

## 'Unfollow' Task - every 24 hours (48?)

Pseudocode

	Unfollow everyone on deletion list if any (initially empty)
	Clear the deletion list
	Find out people I follow who don't follow back
		Add them to the deletion list
		Add them to the unfollowed_archive list
	Quit


## 'Follow' Task - every 2 hours?

Pseudocode

	Limit := 10 (or configured value)
	For each in MyFollowers
		For each in TheirFollowers
			If They.Follow > They.Followers && Limit > 0 (i.e. they are in follower defecit)
				AND They are not in the unfollowed_archive list
				Follow That Person
				Limit -= 1;
	If Limit == 0 Then Exit
