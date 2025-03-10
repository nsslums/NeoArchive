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
  const src =
    "https://devstreaming-cdn.apple.com/videos/streaming/examples/adv_dv_atmos/main.m3u8";
  return (
    <div className="flex gap-4 flex-col xl:flex-row">
      <div className="flex flex-col gap-4 flex-1">
        <Player src={src + "?" + playId} className="w-full" />
        <div className="flex gap-2 flex-col">
          <Skeleton className="h-6 w-[600px]" />
          <Skeleton className="h-4 w-[200px]" />
        </div>
      </div>
      <div>
        <ul className="w-[400px]">
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
