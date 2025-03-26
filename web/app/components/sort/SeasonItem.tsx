import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from "../ui/dropdown-menu";
import { Button } from "../ui/button";
import { MoreVerticalIcon } from "lucide-react";
import { SeasonDetail } from "~/routes/edit.mock";

export function SeasonItem({
  season,
  setSelectedSeasonId,
  setDialogOpen,
}: {
  season: SeasonDetail;
  setSelectedSeasonId: React.Dispatch<React.SetStateAction<number>>;
  setDialogOpen: React.Dispatch<React.SetStateAction<boolean>>;
}) {
  return (
    <div className="w-full p-2 flex justify-between items-center gap-1">
      <span>{season.title}</span>
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
          <DropdownMenuItem
            onSelect={() => {
              setSelectedSeasonId(season.id);
              setDialogOpen(true);
            }}
          >
            <span>Edit</span>
          </DropdownMenuItem>

          <DropdownMenuSeparator />
          <DropdownMenuItem>
            <span className="text-red-800">Delete</span>
          </DropdownMenuItem>
        </DropdownMenuContent>
      </DropdownMenu>
    </div>
  );
}
