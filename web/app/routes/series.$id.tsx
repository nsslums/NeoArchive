import { LoaderFunctionArgs } from "@remix-run/node";
import { Link, useLoaderData } from "@remix-run/react";
import { Skeleton } from "~/components/ui/skeleton";
import type { paths } from "api/schema";
import createClient from "openapi-fetch";
import { parse } from "postcss";

export const loader = async ({ params }: LoaderFunctionArgs) => {
  const client = createClient<paths>({ baseUrl: "http://localhost:1323" });
  const { data: series } = await client.GET("/series/{id}", {
    params: {
      path: { id: Number(params.id) },
    },
  });

  if (!series) {
    throw new Error("NO SERIES data.");
  }

  const { data: season_list } = await client.GET(
    "/anime/season_list/{series_id}",
    {
      params: {
        path: { series_id: series.id },
      },
    }
  );

  if (!season_list) {
    throw new Error("NO season_list data.");
  }

  return { series, season_list };
};

export default function Season() {
  const { series, season_list } = useLoaderData<typeof loader>();

  return (
    <div>
      <div className="flex gap-6">
        <Skeleton className="rounded-xl max-w-[50%] h-auto aspect-video w-[640px]" />
        <div className="flex flex-col gap-4">
          <h1 className="text-2xl">{series.title}</h1>
        </div>
      </div>
      <div className="flex flex-col gap-2 mt-12">
        {season_list.map((season) => (
          <div key={season.id} className="hover:bg-zinc-100">
            <Link to={"/season/" + season.id}>
              <div className="flex gap-4 items-center p-2 rounded-md">
                <div className="w-[208px] aspect-video h-[117px] rounded-lg overflow-hidden">
                  <Skeleton className="rounded-lg flex-shrink-0 w-full h-full object-cover" />
                </div>
                <div className="flex flex-col gap-2 py-2 w-full">
                  <p>{season.title}</p>
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
