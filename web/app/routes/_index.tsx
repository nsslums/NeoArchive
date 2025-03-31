import type { MetaFunction } from "@remix-run/node";
import { useLoaderData } from "@remix-run/react";
import { paths } from "api/schema";
import createClient from "openapi-fetch";
import { FaClockRotateLeft } from "react-icons/fa6";
import { PiArrowsClockwiseBold, PiChatTextBold } from "react-icons/pi";
import { AnimeCard } from "~/components/animeCard";

export const meta: MetaFunction = () => {
  return [
    { title: "NeoArchive" },
    { name: "description", content: "Welcome to NeoArchive!" },
  ];
};

export const loader = async () => {
  const client = createClient<paths>({ baseUrl: "http://localhost:1323" });

  const { data: series_list } = await client.GET("/series_list");
  if (!series_list) {
    throw new Error("NO series_list DATA.");
  }

  return { series_list };
};

export default function Index() {
  const { series_list } = useLoaderData<typeof loader>();
  console.log(series_list);

  return (
    <div>
      <div>
        <p className="text-lg ml-4 mt-8 flex gap-2 items-center">
          <PiChatTextBold />
          おすすめ
        </p>
        <div className="grid grid-cols-1 w-full gap-x-4 gap-y-6 my-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
          {series_list.map((series) => (
            <AnimeCard Title={series.title} href={`/series/${series.id}`} />
          ))}
        </div>
      </div>
      <div>
        <p className="text-lg ml-4 flex gap-2 items-center">
          <PiArrowsClockwiseBold />
          更新されたアニメ
        </p>
        <div className="grid grid-cols-1 w-full gap-x-4 gap-y-6 my-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
          <AnimeCard href="/play/1" />
          <AnimeCard href="/play/1" />
          <AnimeCard href="/play/1" />
          <AnimeCard href="/play/1" />
        </div>
      </div>
      <div>
        <p className="text-lg ml-4 mt-8 flex gap-2 items-center">
          <FaClockRotateLeft />
          履歴
        </p>
        <div className="grid grid-cols-1 w-full gap-x-4 gap-y-6 my-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
          <AnimeCard href="/play/1" />
          <AnimeCard href="/play/1" />
          <AnimeCard href="/play/1" />
          <AnimeCard href="/play/1" />
        </div>
      </div>
    </div>
  );
}
