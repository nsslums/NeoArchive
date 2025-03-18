import { LoaderFunctionArgs } from "@remix-run/node";
import { Link, useLoaderData } from "@remix-run/react";
import { Skeleton } from "~/components/ui/skeleton";
import type { paths } from "api/schema";
import createClient from "openapi-fetch";
import { parse } from "postcss";

export const loader = async ({ params }: LoaderFunctionArgs) => {
  const client = createClient<paths>({ baseUrl: process.env.API_URL });
  // const {data, error} = await client.GET("/anime/episode_list/{season_id}", {
  //   params: {
  //     path: {season_id: Number(params.id)}
  //   }
  // });

  return null;
};

export default function Season() {
  const season_data = useLoaderData<typeof loader>();

  const data = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
  return (
    <div>
      <div className="flex gap-6">
        <img
          className=" rounded-xl"
          src="https://cs1.animestore.docomo.ne.jp/anime_kv/img/27/62/2/27622_1_1.png?1735254239003"
        />
        <div className="flex flex-col gap-4">
          <h1 className="text-2xl">
            ギルドの受付嬢ですが、残業は嫌なのでボスをソロ討伐しようと思います
          </h1>
          <div className="flex gap-2">
            <Skeleton className="h-6 w-[100px]" />
            <Skeleton className="h-6 w-[50px]" />
          </div>
        </div>
      </div>
      <div className="flex flex-col gap-4 mt-12">
        {data.map((val, index) => (
          <div key={index}>
            <Link to={"/play/" + val}>
              <div className="flex gap-4 items-center">
                <div className="w-[208px] aspect-video h-[117px] rounded-lg overflow-hidden">
                  <img
                    className="rounded-lg duration-300 hover:scale-105 flex-shrink-0 w-full h-full object-cover"
                    src="https://cs1.animestore.docomo.ne.jp/anime_kv/img/27/62/2/0/01/27622001_1_6.png?1736930767690"
                    alt=""
                  />
                </div>
                <div className="flex flex-col gap-2 py-2 w-full">
                  <p>第{val}話</p>
                  <Skeleton className="h-4 w-[400px]" />
                  <Skeleton className="h-12 w-full" />
                </div>
              </div>
            </Link>
          </div>
        ))}
      </div>
    </div>
  );
}
