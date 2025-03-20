import type { MetaFunction } from "@remix-run/node";
import { FaClockRotateLeft } from "react-icons/fa6";
import { PiArrowsClockwiseBold } from "react-icons/pi";
import { AnimeCard } from "~/components/animeCard";
import Player from "~/components/player";
import { Button } from "~/components/ui/button";

export const meta: MetaFunction = () => {
  return [
    { title: "NeoArchive" },
    { name: "description", content: "Welcome to NeoArchive!" },
  ];
};

export default function Index() {
  return (
    <div>
      <div>
        <p className="text-lg ml-4 flex gap-2 items-center">
          <PiArrowsClockwiseBold />
          更新されたアニメ
        </p>
        <div className="flex flex-wrap w-full gap-x-4 gap-y-6 justify-center my-4">
          <AnimeCard Title="test" Description="test" href="/play/1" />
          <AnimeCard Title="test" Description="test" href="/play/1" />
          <AnimeCard Title="test" Description="test" href="/play/1" />
          <AnimeCard Title="test" Description="test" href="/play/1" />
          <AnimeCard Title="test" Description="test" href="/play/1" />
          <AnimeCard Title="test" Description="test" href="/play/1" />
          <AnimeCard Title="test" Description="test" href="/play/1" />
          <AnimeCard Title="test" Description="test" href="/play/1" />
          <AnimeCard Title="test" Description="test" href="/play/1" />
          <AnimeCard Title="test" Description="test" href="/play/1" />
        </div>
      </div>
      <div>
        <p className="text-lg ml-4 mt-8 flex gap-2 items-center">
          <FaClockRotateLeft />
          履歴
        </p>
        <div className="flex flex-wrap w-full gap-x-4 gap-y-6 justify-center my-4">
          <AnimeCard Title="test" Description="test" href="/season/1"/>
          <AnimeCard Title="test" Description="test" href="/season/1"/>
          <AnimeCard Title="test" Description="test" href="/season/1"/>
          <AnimeCard Title="test" Description="test" href="/season/1"/>
          <AnimeCard Title="test" Description="test" href="/season/1"/>
          <AnimeCard Title="test" Description="test" href="/season/1"/>
          <AnimeCard Title="test" Description="test" href="/season/1"/>
          <AnimeCard Title="test" Description="test" href="/season/1"/>
          <AnimeCard Title="test" Description="test" href="/season/1"/>
          <AnimeCard Title="test" Description="test" href="/season/1"/>
        </div>
      </div>
    </div>
  );
}
