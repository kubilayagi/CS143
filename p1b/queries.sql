select concat(a.first, ' ', a.last) as Names
from Actor as a, Movie as m, MovieActor as x
where a.id = x.aid and m.id = x.mid and m.title = 'Die Another Day';
-- Gets names of all the actors in the movie 'Die Another Day' 

select count(aid) as Number_of_Actors_in_Multiple_Movies
from (select aid, count(aid)
	  from MovieActor
	  group by aid
	  having count(aid) > 1) as Frequent_Actors;
-- Gets count of all the actors who acted in multiple movies

select pair as Director_Actor_Pair, count(pair) as Collaborations
from (select concat(d.first, ' ', d.last, ' and ', a.first, ' ', a.last) as pair
	  from Director as d, Actor as a, MovieActor as ma, MovieDirector as md
	  where d.id = md.did and md.mid = ma.mid and ma.aid = a.id) as Director_Actor_Pairs
group by pair
having count(pair) >= 3;
-- Gets pairs of Directors and Actors that have worked together 3 or more times on different movies, along with the number of collaborations