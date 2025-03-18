import { LoaderFunctionArgs } from "@remix-run/node";
import { Link, useLoaderData } from "@remix-run/react";
import { Slice } from "lucide-react";
import { useEffect } from "react";
import Player from "~/components/player";
import { Skeleton } from "~/components/ui/skeleton";

export const loader = ({ params }: LoaderFunctionArgs) => {
  const seasonId = params.id;
  return seasonId;
};

export default function Play() {
  const playId = useLoaderData<typeof loader>();

  const data = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
  // const src =
  //   "https://devstreaming-cdn.apple.com/videos/streaming/examples/adv_dv_atmos/main.m3u8";
  const src =
    "http://localhost:8080/master.m3u8";
  const anime_data = {subtitle: "Ensemble for Polaris -私たちの約束-"}
  return (
    <div className="flex gap-4 flex-col lg:flex-row">
      <div className="flex flex-col gap-4 flex-1">
        <Player
          src={src + "?" + playId}
          srcTitle={anime_data.subtitle}
          className="w-full"
        />
        <div className="flex gap-2 flex-col">
          <Skeleton className="h-6 w-[600px]" />
          <Skeleton className="h-4 w-[200px]" />
        </div>
      </div>
      <div>
        <div className="w-full p-4 rounded-lg shadow-lg lg:w-[400px] lg:h-[calc((1024px-400px-1rem)*9/16)] xl:h-[calc((1280px-400px-1rem)*9/16)] 2xl:h-[calc((1536px-400px-1rem)*9/16)]">
          <h2 className=" text-lg font-semibold mb-2">次に再生</h2>
          <ul className="max-h-[316px] lg:max-h-[calc((1024px-400px-1rem)*9/16-3.25rem)] xl:max-h-[calc((1280px-400px-1rem)*9/16-3.25rem)] 2xl:max-h-[calc((1536px-400px-1rem)*9/16-3.25rem)] overflow-auto">
            {data.map((val, index) => (
              <li key={index} className="relative py-2">
                <Link to={`/play/${val}`} className="flex gap-2">
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
        <ul className="mt-4">
          {data.map((val, index) => (
            <li key={index} className="relative p-2">
              <Link to={`/play/${val}`} className="flex gap-2">
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
