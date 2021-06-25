<?php
	
	print_r(json_decode('["ROLE_ADMIN"]'));


/*
SUPPLY

SELECT hour, SUM(formatloads) AS FormatLoads, SUM(impressions) AS Impressions, SUM(starts) AS Statrs, concat('$', round(SUM(usd_revenue), 2)) AS Revenue, concat('$', round(SUM(usd_cost), 2)) AS Coste, 

concat( round( (SUM(starts) / SUM(formatLoads) * 100), 2), '%') AS FillRate

FROM supply_monthly_report 

INNER JOIN website ON website.id = supply_monthly_report.website_id
INNER JOIN website_zone ON website_zone.id = supply_monthly_report.website_zone_id

WHERE date = '2019-11-28'

GROUP BY  hour

ORDER BY hour DESC



DEMANDA

SELECT website.sitename, website_zone.name AS supply, tag.name AS demand_partner, SUM(tag_requests) AS TagRequests, SUM(impressions) AS Impressions, concat('$', round(SUM(usd_revenue), 2)) AS Revenue, concat('$', round(SUM(usd_cost), 2)) AS Coste, 

concat( round( (SUM(impressions) / SUM(tag_requests) * 100), 2), '%') AS FillRate

FROM demand_monthly_report 

INNER JOIN website ON website.id = demand_monthly_report.website_id
INNER JOIN website_zone ON website_zone.id = demand_monthly_report.website_zone_id
INNER JOIN tag ON tag.id = demand_monthly_report.tag_id

WHERE date = '2019-11-29' AND hour = 11

GROUP BY  supply, demand_partner

ORDER BY Impressions DESC
*/