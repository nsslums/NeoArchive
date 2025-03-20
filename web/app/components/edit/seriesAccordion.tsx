import { SeriesDetail } from "~/routes/edit.$id";
import {
  Accordion,
  AccordionContent,
  AccordionItem,
  AccordionTrigger,
} from "../ui/accordion";
import React from "react";
import { Skeleton } from "../ui/skeleton";

type Props = {
  data: SeriesDetail;
  children: React.ReactNode;
};

export function SeriesAccordion({ data, children }: Props) {
  return (
    <Accordion type="multiple" className="w-full" defaultValue={[data.id.toString()]}>
      <AccordionItem value={data.id.toString()}>
        <AccordionTrigger>
          <div className="flex flex-row gap-2 items-center">
            <Skeleton className="w-[75px] aspect-video" />
            <span className="flex-1">{data.title}</span>
          </div>
        </AccordionTrigger>
        <AccordionContent>{children}</AccordionContent>
      </AccordionItem>
    </Accordion>
  );
}
