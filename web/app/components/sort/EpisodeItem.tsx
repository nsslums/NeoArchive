import { EpisodeDetail } from "~/routes/edit.mock";
import { Skeleton } from "../ui/skeleton";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from "../ui/dropdown-menu";
import { Button } from "../ui/button";
import { MoreVerticalIcon } from "lucide-react";
import React, { useEffect, useState } from "react";
import { Input } from "../ui/input";

type TextOrInputProps = {
  name: string;
  value?: string;
  isEdit: boolean;
  onChange: (e: React.ChangeEvent<HTMLInputElement>) => void;
};

const TextOrInput: React.FC<TextOrInputProps> = ({
  name,
  value,
  isEdit,
  onChange,
}) => {
  return isEdit ? (
    <Input type="text" name={name} value={value} onChange={onChange} />
  ) : (
    <p>{value}</p>
  );
};

type EpisodeItemProps = {
  episode: EpisodeDetail;
  className?: string;
  setAnimeData?: (editData: EpisodeDetail) => void;
};

export function EpisodeItem({
  episode,
  className,
  setAnimeData,
}: EpisodeItemProps) {
  const [isEdit, setIsEdit] = useState(false);
  const [data, setData] = useState<EpisodeDetail>(episode);

  function handleChange(e: React.ChangeEvent<HTMLInputElement>) {
    const newData = { ...data, [e.target.name]: e.target.value };
    setData(newData);
    if (!setAnimeData) return;
    setAnimeData(newData);
  }

  return (
    <div className={`flex gap-4 relative ${className}`}>
      <div className="flex justify-center items-center ml-0.5 gap-[10px]">
        <Skeleton className="w-48 aspect-video" />
      </div>
      <div>
        <TextOrInput
          name="number"
          value={data.number}
          isEdit={isEdit}
          onChange={handleChange}
        />

        <TextOrInput
          name="subtitle"
          value={data.subtitle}
          isEdit={isEdit}
          onChange={handleChange}
        />
      </div>
      <div className="absolute top-3 right-3">
        <DropdownMenu>
          <DropdownMenuTrigger asChild>
            <Button
              variant="ghost"
              className="flex size-5 text-muted-foreground"
              size="icon"
            >
              <MoreVerticalIcon />
              <span className="sr-only">Open Menu</span>
            </Button>
          </DropdownMenuTrigger>
          <DropdownMenuContent align="end" className="w-32">
            <DropdownMenuItem onClick={() => setIsEdit((prev) => !prev)}>
              Edit
            </DropdownMenuItem>
            <DropdownMenuSeparator />
            <DropdownMenuItem>
              <span className="text-red-800">Delete</span>
            </DropdownMenuItem>
          </DropdownMenuContent>
        </DropdownMenu>
      </div>
    </div>
  );
}
