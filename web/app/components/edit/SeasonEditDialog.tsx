import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "../ui/dialog";
import { Button } from "../ui/button";
import { CastDetail, SeasonDetail } from "~/routes/edit.mock";
import { Label } from "../ui/label";
import { Input } from "../ui/input";
import { Textarea } from "../ui/textarea";
import React, { useEffect, useState } from "react";
import { InputTags } from "./InputTags";

export function SeasonEditDialog({
  season_init,
  open,
  handleDialogChange,
  setAnimeData,
}: {
  season_init: SeasonDetail;
  open?: boolean;
  handleDialogChange: (e: boolean) => void;
  setAnimeData: (value: SeasonDetail) => void;
}) {
  const [season, setSeason] = useState<SeasonDetail>(season_init);

  useEffect(() => {
    setSeason(season_init);
  }, [season_init]);

  function handleChange(
    e:
      | React.ChangeEvent<HTMLInputElement>
      | React.ChangeEvent<HTMLTextAreaElement>
  ) {
    setSeason((prev) => ({ ...prev, [e.target.name]: e.target.value }));
  }

  function handleTagsChange(value: CastDetail[]) {
    setSeason((prev) => ({ ...prev, casts: value }));
  }

  return (
    <Dialog open={open} onOpenChange={handleDialogChange}>
      <DialogContent className="sm:max-w-[425px] md:max-w-[700px]">
        <DialogHeader>
          <DialogTitle>Edit Season</DialogTitle>
          <DialogDescription>
            Make changes to season metadta here. Click save when you're done.
          </DialogDescription>
        </DialogHeader>
        <div className="grid gap-4 py-4">
          <div className="grid grid-cols-5 items-center gap-4">
            <Label htmlFor="title" className="text-right">
              Title
            </Label>
            <Input
              id="title"
              name="title"
              value={season.title}
              className="col-span-4"
              onChange={handleChange}
            />
          </div>
          <div className="grid grid-cols-5 items-center gap-4">
            <Label htmlFor="synopsis" className="text-right">
              Synopsis
            </Label>
            <Textarea
              id="synopsis"
              name="synopsis"
              value={season.synopsis}
              className="col-span-4 h-64"
              onChange={handleChange}
            />
          </div>
          <div className="grid grid-cols-5 items-center gap-4">
            <Label htmlFor="production" className="text-right">
              Production
            </Label>
            <Input
              id="production"
              name="production"
              value={season.production}
              className="col-span-4"
              onChange={handleChange}
            />
          </div>
          <div className="grid grid-cols-5 items-center gap-4">
            <Label htmlFor="casts" className="text-right">
              Casts
            </Label>
            <InputTags value={season.casts} onChange={handleTagsChange} />
          </div>
          <div className="grid grid-cols-5 items-center gap-4">
            <Label htmlFor="cours" className="text-right">
              Cours
            </Label>
            <Input
              id="cours"
              name="cours"
              value={season.cours}
              className="col-span-4"
              onChange={handleChange}
            />
          </div>
        </div>
        <DialogFooter>
          <Button
            type="submit"
            onClick={() => {
              setAnimeData(season);
              handleDialogChange(false);
            }}
          >
            Save changes
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  );
}
