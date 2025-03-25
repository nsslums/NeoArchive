import { XIcon } from "lucide-react";
import {
  forwardRef,
  useState,
} from "react";
import { Input } from "../ui/input";
import { Button } from "../ui/button";
import { Badge } from "../ui/badge";
import { CastDetail } from "~/routes/edit.mock";

type InputTagsProps = {
  value: CastDetail[];
  onChange: (e: CastDetail[]) => void;
};

export const InputTags = forwardRef<HTMLInputElement, InputTagsProps>(
  ({ value, onChange, ...props }, ref) => {
    const [pendingDataPoint, setPendingDataPoint] = useState("");

    const addPendingDataPoint = () => {
      if (pendingDataPoint) {
        const setData: CastDetail = { id: Date.now(), name: pendingDataPoint };
        const newDataPoints = new Set([...value, setData]);
        onChange(Array.from(newDataPoints));
        setPendingDataPoint("");
      }
    };

    return (
      <div className="flex gap-4">
        <div className="flex">
          <Input
            value={pendingDataPoint}
            onChange={(e) => setPendingDataPoint(e.target.value)}
            onKeyDown={(e) => {
              if (e.key === "Enter") {
                e.preventDefault();
                addPendingDataPoint();
              } else if (e.key === "," || e.key === " ") {
                e.preventDefault();
                addPendingDataPoint();
              }
            }}
            className="rounded-r-none w-32"
            {...props}
            ref={ref}
          />
          <Button
            type="button"
            variant="secondary"
            className="rounded-l-none border border-l-0"
            onClick={addPendingDataPoint}
          >
            Add
          </Button>
        </div>
        <div className="flex-1">
          <div className="border rounded-md min-h-[2.5rem] overflow-y-auto p-2 flex gap-2 flex-wrap items-center">
            {value.map((item, idx) => (
              <Badge key={idx} variant="secondary">
                {item.name}
                <button
                  type="button"
                  className="w-3 ml-2"
                  onClick={() => {
                    onChange(value.filter((i) => i !== item));
                  }}
                >
                  <XIcon className="w-3" />
                </button>
              </Badge>
            ))}
          </div>
        </div>
      </div>
    );
  }
);
