import type { MetaFunction } from "@remix-run/node";
import { AnimeCard } from "~/components/animeCard";
import Player from "~/components/player";
import { Button } from "~/components/ui/button";

export const meta: MetaFunction = () => {
  return [
    { title: "IASS" },
    { name: "description", content: "Welcome to IASS!" },
  ];
};

export default function Index() {
  return (
    <div>
      <Button>Click Me</Button>
      <AnimeCard Title="test" Description="test" />
      <div className="w-3/4">
        <Player src= "https://devstreaming-cdn.apple.com/videos/streaming/examples/adv_dv_atmos/main.m3u8" />
      </div>
    </div>
  );
}
