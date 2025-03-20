import { LoaderFunctionArgs } from "@remix-run/node";
import { Outlet, useLoaderData } from "@remix-run/react";
import type { paths } from "api/schema";
import createClient from "openapi-fetch";
import { useState } from "react";
import {
  arrayMove,
  SortableContext,
  verticalListSortingStrategy,
} from "@dnd-kit/sortable";
import { Droppable } from "~/components/sort/Droppable";
import {
  Active,
  DndContext,
  DragEndEvent,
  DragOverEvent,
  DragOverlay,
  DragStartEvent,
  MouseSensor,
  Over,
  pointerWithin,
  UniqueIdentifier,
  useSensor,
  useSensors,
} from "@dnd-kit/core";
import { Sortable } from "~/components/sort/Sortable";
import { SortableItem } from "~/components/sort/SortableItem";
import { SortableDrop } from "~/components/sort/SortableDrop";
import { v4 as uuidv4 } from "uuid";
import { SeriesAccordion } from "~/components/edit/seriesAccordion";
import { MenuSeasonItem } from "~/components/sort/MenuSeasonItem";
import { Card } from "~/components/ui/card";
import { Skeleton } from "~/components/ui/skeleton";
import { EpisodeItem } from "~/components/sort/EpisodeItem";

type TagDetail = {
  id: number;
  name: string;
};
type CastDetail = {
  id: number;
  name: string;
};
export type EpisodeDetail = {
  id: number;
  season_id?: number;
  video_id?: number;
  display_number?: number;
  number?: string;
  subtitle: string;
};
type SeasonDetail = {
  id: number;
  series_id?: number;
  casts?: CastDetail[];
  display_number?: number;
  title: string;
  synopsis?: string;
  cours?: string;
  production?: string;
  episodes: EpisodeDetail[];
};
export type SeriesDetail = {
  id: number;
  title: string;
  tags?: TagDetail[];
  season: SeasonDetail[];
};
type AnimeDetail = {
  id: string;
  name: string;
  series: SeriesDetail[];
};
type AnimeIndexes = {
  series: number;
  season: number;
};

export default function SeasonEdit() {
  function habdleDragStart(event: DragStartEvent): void {
    throw new Error("Function not implemented.");
  }

  function handleDragOver(event: DragOverEvent): void {
    throw new Error("Function not implemented.");
  }

  const data = [
    1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20,
  ];

  return (
    <div>
      <div className="flex gap-12">
        <SortableContext
          items={data}
          key="testtest"
          id="testtest"
          strategy={verticalListSortingStrategy}
        >
          <Card className="flex flex-col gap-2 p-4">
            {data.map((val) => (
              <Sortable key={val} id={val} className="hover:bg-zinc-100 w-4 h-12">
                {val}
              </Sortable>
            ))}
          </Card>
        </SortableContext>
      </div>
    </div>
  );
}
