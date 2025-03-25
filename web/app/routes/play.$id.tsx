import { LoaderFunctionArgs } from "@remix-run/node";
import { Link, useLoaderData } from "@remix-run/react";
import Player from "~/components/player";
import { Card } from "~/components/ui/card";
import { Skeleton } from "~/components/ui/skeleton";
import { EpisodeDetail, SeasonDetail } from "./edit.mock";
import createClient from "openapi-fetch";
import { paths } from "api/schema";

export const loader = async ({ params }: LoaderFunctionArgs) => {
  // const client = createClient<paths>({ baseUrl: process.env.API_URL });
  // const { data: episode } = await client.GET("/anime/episode/{id}", {
  //   params: {
  //     path: { id: Number(params.id) },
  //   },
  // });

  // if (!episode) return;

  // const { data: season } = await client.GET("/anime/episode_list/{season_id}", {
  //   params: {
  //     path: { season_id: episode.season_id },
  //   },
  // });
  // if (!season) {
  //   return;
  // }

  // return {season: season, episode: episode}

  const episode: EpisodeDetail = {
    id: Number(params.id),
    number: "1",
    subtitle: "Episode 1-1",
    display_number: 0,
    video_id: Number(params.id),
    season_id: 111111,
  };

  const episode1: EpisodeDetail = {
    id: 1000,
    number: "1",
    subtitle: "Episode 1-1",
    display_number: 0,
    video_id: 1000,
    season_id: 111111,
  };

  const episode2: EpisodeDetail = {
    id: 2000,
    number: "1",
    subtitle: "Episode 1-1",
    display_number: 0,
    video_id: 2000,
    season_id: 111111,
  };

  const season: SeasonDetail = {
    id: 101,
    title: "1",
    display_number: 0,
    episodes: [
      episode1,
      episode2,
      episode1,
      episode2,
      episode1,
      episode2,
      episode1,
      episode2,
      episode1,
      episode2,
    ],
    casts: [],
    synopsis: "",
    cours: "",
    production: "",
  };


  return { season: season, episode: episode };
};

export default function Play() {
  const { season, episode } = useLoaderData<typeof loader>();

  const src =
    "https://devstreaming-cdn.apple.com/videos/streaming/examples/adv_dv_atmos/main.m3u8";
  // const src =
  //   "http://localhost:8080/master.m3u8";

  return (
    <div className="flex gap-4 flex-col lg:flex-row">
      <div className="flex flex-col gap-4 flex-1">
        <Player
          src={src + "?" + episode.video_id}
          srcTitle={episode.subtitle}
          className="w-full"
        />
        <div className="flex gap-2 flex-col">
          <Skeleton className="h-6 w-[600px]" />
          <Skeleton className="h-4 w-[200px]" />
        </div>
      </div>
      <div>
        <Card className="w-full p-4 lg:w-[400px] lg:h-[calc((1024px-400px-1rem)*9/16)] xl:h-[calc((1280px-400px-1rem)*9/16)] 2xl:h-[calc((1536px-400px-1rem)*9/16)]">
          <h2 className=" text-lg font-semibold mb-2">次に再生</h2>
          <ul className="max-h-[316px] lg:max-h-[calc((1024px-400px-1rem)*9/16-3.25rem)] xl:max-h-[calc((1280px-400px-1rem)*9/16-3.25rem)] 2xl:max-h-[calc((1536px-400px-1rem)*9/16-3.25rem)] overflow-auto">
            {season.episodes.map((ep, index) => (
              <li key={index} className="relative py-2">
                <Link to={`/play/${ep.id}`} className="flex gap-2">
                  <Skeleton className="w-[120px] h-20" />
                  <div className="flex flex-col gap-2 py-2">
                    <Skeleton className="h-4 w-[200px]" />
                    <Skeleton className="h-4 w-[150px]" />
                  </div>
                </Link>
              </li>
            ))}
          </ul>
        </Card>
        <ul className="mt-4">
          {season.episodes.map((ep, index) => (
            <li key={index} className="relative p-2">
              <Link to={`/play/${ep.id}`} className="flex gap-2">
                <Skeleton className="w-[120px] h-20" />
                <div className="flex flex-col gap-2 py-2">
                  <Skeleton className="h-4 w-[200px]" />
                  <Skeleton className="h-4 w-[150px]" />
                </div>
              </Link>
            </li>
          ))}
        </ul>
      </div>
    </div>
  );
}
